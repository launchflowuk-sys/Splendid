<?php
/** WP-CLI only. Creates missing DRAFT pages on local/development/staging. Never overwrites. */
if (!defined('WP_CLI') || !WP_CLI) { exit('Run with WP-CLI.'); }
$env = wp_get_environment_type();
if (!in_array($env, array('local','development','staging'), true)) {
    WP_CLI::error('This importer is restricted to a local/development/staging installation.');
}
$path = dirname(__DIR__) . '/content/pages.json';
$rows = json_decode(file_get_contents($path), true);
if (!is_array($rows) || count($rows) !== 38) { WP_CLI::error('Expected the complete 38-page manifest.'); }
$apply = getenv('SPLENDID_IMPORT_APPLY') === '1';
usort($rows, function($a, $b) {
    return substr_count(trim($a['route'], '/'), '/') <=> substr_count(trim($b['route'], '/'), '/');
});
$ids = array(); $created = array();
foreach ($rows as $row) {
    $route = trim($row['route'], '/');
    $slugpath = $route === '' ? 'home' : $route;
    $existing = get_page_by_path($slugpath, OBJECT, 'page');
    if ($existing) {
        $ids[$row['route']] = $existing->ID;
        WP_CLI::log('KEEP existing page ' . $slugpath . ' (' . $existing->ID . ')');
        continue;
    }
    if (!$apply) { WP_CLI::log('WOULD CREATE DRAFT ' . $slugpath); continue; }
    $parts = explode('/', $slugpath); $slug = array_pop($parts); $parent = 0;
    if ($parts) {
        $parentroute = '/' . implode('/', $parts);
        if (!isset($ids[$parentroute])) { WP_CLI::error('Missing parent for ' . $slugpath); }
        $parent = $ids[$parentroute];
    }
    // Start with captured markup for the developer to convert into editable blocks.
    // Legal source notices are not imported; require the WordPress policy drafts instead.
    $body = $row['template'] === 'legal'
      ? '<p>Policy draft pending configuration and business review. Use briefs/04-legal-and-client-confirmations.md.</p>'
      : $row['body_html'];
    $id = wp_insert_post(array(
        'post_type'=>'page','post_status'=>'draft','post_title'=>$row['title'],
        'post_name'=>$slug,'post_parent'=>$parent,'post_content'=>$body,
        'comment_status'=>'closed','ping_status'=>'closed'
    ), true);
    if (is_wp_error($id)) { WP_CLI::error($id->get_error_message()); }
    update_post_meta($id, '_splendid_reference_route', $row['route']);
    update_post_meta($id, '_splendid_reference_template', $row['template']);
    update_post_meta($id, '_splendid_seo_title', $row['seo_title']);
    update_post_meta($id, '_splendid_meta_description', $row['meta_description']);
    $ids[$row['route']] = $id; $created[$row['route']] = $id;
    WP_CLI::log('CREATED DRAFT ' . $slugpath . ' (' . $id . ')');
}
if ($apply) {
    update_option('splendid_handover_page_ids', $ids, false);
    WP_CLI::success('Created ' . count($created) . ' draft pages. Existing pages preserved. Homepage, menus, media, theme and publishing remain to be configured.');
} else { WP_CLI::success('Dry run only. Set SPLENDID_IMPORT_APPLY=1 to create missing drafts.'); }
