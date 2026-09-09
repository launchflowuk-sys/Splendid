<?php
/**
 * Product, area, article and FAQ data from the approved design.
 *
 * Generated from handover/content/original-data.json by tools/build-data.py.
 * These values seed the imported pages and validate enquiry submissions; the
 * published copy afterwards lives in the pages themselves and is editable.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * The full source dataset.
 *
 * @return array
 */
function splendid_data() {
	return array(
		'company' => array(
			'phone' => '020 7998 6802',
			'tel' => '02079986802',
			'email' => 'info@splendidglazing.co.uk',
			'address' => '758 Sidcup Road, London, SE9 3NS',
		),
		'windows' => array(
			array(
				'slug' => 'double-glazing',
				'name' => 'Double glazing',
				'tag' => 'Everyday comfort',
				'intro' => 'A warmer welcome. A quieter home.',
				'text' => 'Two panes of glass, one thoughtful upgrade. Replacement double glazing helps reduce draughts and brings a fresh finish to the rooms you use every day.',
				'features' => array(
					'A choice of frame materials',
					'Glazing tailored to each room',
					'Opening styles to suit your home',
				),
				'detail' => 'The right window starts with the room around it. We help you consider ventilation, privacy, maintenance and the appearance of your existing property before measuring for a replacement.',
			),
			array(
				'slug' => 'triple-glazing',
				'name' => 'Triple glazing',
				'tag' => 'Extra insulation',
				'intro' => 'Make comfort part of the design.',
				'text' => 'An additional pane of glass offers another option when thermal insulation is a priority. Explore whether triple glazing is the right fit for your home.',
				'features' => array(
					'Three-pane glazing options',
					'Room-by-room specification',
					'Frame and glass chosen together',
				),
				'detail' => 'Performance depends on the complete window, its size and its installation. Ask for the whole-window U-value and compare it with a double-glazed alternative before choosing. Availability and final specification are confirmed with your quote.',
			),
			array(
				'slug' => 'upvc-windows',
				'name' => 'uPVC windows',
				'tag' => 'Easy living',
				'intro' => 'Beautifully simple. Every day.',
				'text' => 'Clean lines, versatile styles and easy care. uPVC windows are a practical choice for refreshing a family home without adding a demanding maintenance routine.',
				'features' => array(
					'Simple care and cleaning',
					'Traditional and modern styles',
					'Colour and hardware choices',
				),
				'detail' => 'From a single bedroom window to a whole-house refresh, choose a consistent finish while adapting the openings to each space. Your survey establishes dimensions, access and ventilation requirements.',
			),
			array(
				'slug' => 'aluminium-windows',
				'name' => 'Aluminium windows',
				'tag' => 'Architectural lines',
				'intro' => 'Less frame. More of the view.',
				'text' => 'Refined profiles and a contemporary finish put daylight at the centre of your home. Aluminium is a natural partner for modern renovations and extensions.',
				'features' => array(
					'Refined, contemporary profiles',
					'A wide choice of finishes',
					'Suitable for architectural projects',
				),
				'detail' => 'Consider how the frame colour will work with your brickwork, doors and interior palette. We can discuss opening configurations and glazing to bring a coherent look to your project.',
			),
			array(
				'slug' => 'sash-windows',
				'name' => 'Sash windows',
				'tag' => 'Timeless character',
				'intro' => 'Keep the character. Renew the comfort.',
				'text' => 'Preserve the proportions that make a period home special. Sliding sash styles bring familiar elegance to terraces, townhouses and traditional properties.',
				'features' => array(
					'Traditional vertical proportions',
					'Choice of decorative details',
					'Material and finish options',
				),
				'detail' => 'Bring photographs of your current windows when you enquire. Meeting rails, glazing bars and frame proportions all affect the finished look. If your property has heritage restrictions, confirm the necessary approvals before ordering.',
			),
			array(
				'slug' => 'bay-windows',
				'name' => 'Bay windows',
				'tag' => 'Light from every angle',
				'intro' => 'A little more room to love.',
				'text' => 'Turn a window into the focal point of your room. Bay designs create an expansive outlook and make the most of the light through the day.',
				'features' => array(
					'Angled or curved arrangements',
					'Designed around your existing opening',
					'A considered match for your home',
				),
				'detail' => 'A bay is a group of windows working as one. Accurate measurements and an assessment of the existing structure are essential to achieving a balanced, weather-tight installation.',
			),
		),
		'doors' => array(
			array(
				'slug' => 'composite-doors',
				'name' => 'Composite doors',
				'tag' => 'First impressions',
				'intro' => 'An entrance that feels like you.',
				'text' => 'Set the tone before you step inside. Explore composite entrance doors with characterful colours, glazing details and hardware that complete your home.',
				'features' => array(
					'Distinctive colours and door styles',
					'Privacy glazing options',
					'Coordinated handles and hardware',
				),
				'detail' => 'Choose the door as a complete set: style, colour, glass, threshold and locking specification. Ask the team about the construction, tested security specification and warranty for your chosen product.',
			),
			array(
				'slug' => 'upvc-doors',
				'name' => 'uPVC doors',
				'tag' => 'Practical by design',
				'intro' => 'A dependable door. A fresh look.',
				'text' => 'A versatile choice for front, side and rear entrances. uPVC doors combine a clean appearance with straightforward care.',
				'features' => array(
					'Glazed and panelled styles',
					'Coordinated window finishes',
					'Easy-to-clean surfaces',
				),
				'detail' => 'Your choice of glass can change both the light and privacy of an entrance. We help you work through practical details such as opening direction, threshold height and everyday access.',
			),
			array(
				'slug' => 'aluminium-doors',
				'name' => 'Aluminium doors',
				'tag' => 'Modern living',
				'intro' => 'Make a stronger design statement.',
				'text' => 'Bring crisp architectural lines to your entrance or extension with aluminium doors in a finish chosen for your property.',
				'features' => array(
					'Contemporary frame profiles',
					'Colour-led design choices',
					'Options for larger openings',
				),
				'detail' => 'A successful design connects the outside and inside. Consider matching adjacent windows and agreeing the glazing layout early, particularly when coordinating with a builder.',
			),
			array(
				'slug' => 'bifold-doors',
				'name' => 'Bifold doors',
				'tag' => 'Open up your home',
				'intro' => 'Let the outside become part of inside.',
				'text' => 'Fold back the boundary between your home and garden. Bifold doors create a generous opening for long lunches, warm evenings and everyday living.',
				'features' => array(
					'Folding panel configurations',
					'Inward or outward options',
					'A frame finish to suit your extension',
				),
				'detail' => 'Panel count, stacking direction and everyday access matter just as much as appearance. We help you consider furniture, patio levels and clear opening width as part of your design.',
			),
			array(
				'slug' => 'patio-doors',
				'name' => 'Sliding patio doors',
				'tag' => 'A wider perspective',
				'intro' => 'An uninterrupted connection.',
				'text' => 'Expansive glass and a smooth sliding opening bring your garden into view. A considered choice where you want light without a door swinging into the room.',
				'features' => array(
					'Space-saving sliding operation',
					'Generous glazed panels',
					'A clean contemporary finish',
				),
				'detail' => 'Sliding doors keep panels within their track, making them useful beside furniture or a compact patio. Discuss panel arrangement, ventilation and threshold details at your survey.',
			),
			array(
				'slug' => 'french-doors',
				'name' => 'French doors',
				'tag' => 'Classic connection',
				'intro' => 'Twice the welcome.',
				'text' => 'A familiar pair of glazed doors can change the way a room feels. French doors offer an elegant link to a patio, balcony or garden.',
				'features' => array(
					'Traditional paired-door design',
					'Glazing and finish choices',
					'A natural fit for classic homes',
				),
				'detail' => 'Think about where the leaves will open and how you use the room throughout the year. Side panels can add light when the opening allows, and privacy glass can soften a neighbouring outlook.',
			),
		),
		'areas' => array(
			array(
				'sidcup',
				'Sidcup',
				'From established family houses around the high street to homes in Blackfen, a thoughtful window upgrade starts with the character of your street.',
			),
			array(
				'eltham',
				'Eltham',
				'Consider traditional window proportions, practical opening styles and finishes that sit comfortably beside existing brickwork.',
			),
			array(
				'bexley',
				'Bexley',
				'For a period-inspired refresh or a contemporary extension, we can discuss a coordinated approach to your glazing.',
			),
			array(
				'bromley',
				'Bromley',
				'Balance kerb appeal with everyday comfort, whether you are replacing a single entrance or planning a larger home renovation.',
			),
			array(
				'chislehurst',
				'Chislehurst',
				'Thoughtful detailing matters. Bring your ideas for frame colours, glazing bars and door furniture to your project conversation.',
			),
			array(
				'south-east-london',
				'South East London',
				'Explore glazing for terraces, semi-detached homes and modern extensions, with a design that respects the property around it.',
			),
			array(
				'kent',
				'North Kent',
				'Planning a project beyond Sidcup? Share your postcode and requirements so the team can confirm coverage and survey availability.',
			),
		),
		'articles' => array(
			array(
				'slug' => 'choosing-your-window-material',
				'title' => 'uPVC or aluminium: where to begin?',
				'category' => 'WINDOW GUIDE',
				'intro' => 'Start with the way you want your home to look, then consider the practical details.',
				'sections' => array(
					array(
						'The look of your home',
						'uPVC works across many traditional and contemporary styles. Aluminium is often chosen for its refined profiles and architectural appearance. Compare physical samples beside your brickwork if possible.',
					),
					array(
						'Think about care',
						'Both materials can be straightforward to care for, but hardware and seals need attention too. Ask for the maintenance instructions for the exact product you choose.',
					),
					array(
						'Compare complete specifications',
						'Frame material is only part of the decision. Ask about glazing, opening arrangements, ventilation, installation and the warranty. Compare written quotations on the same basis.',
					),
				),
			),
			array(
				'slug' => 'bifold-or-sliding-doors',
				'title' => 'Bifold or sliding? Find your garden connection.',
				'category' => 'DESIGN NOTES',
				'intro' => 'Two ways to open up a room, with different advantages for the way you live.',
				'sections' => array(
					array(
						'The opening',
						'Bifolds gather to one side to create a broad opening. Sliding doors move within a track, keeping their footprint compact while retaining a large glazed view.',
					),
					array(
						'The everyday details',
						'Consider where furniture sits, how often you use the garden and whether you need a convenient everyday access panel. The best choice works just as well in February as it does in July.',
					),
					array(
						'Plan it together',
						'Discuss thresholds and finished floor levels with your installer and builder before ordering. The wider project can affect drainage, structure and the final specification.',
					),
				),
			),
			array(
				'slug' => 'planning-your-window-replacement',
				'title' => 'Your window project, without the guesswork.',
				'category' => 'PROJECT PLANNING',
				'intro' => 'A few useful details can make your first conversation much more productive.',
				'sections' => array(
					array(
						'Before you enquire',
						'Make a room-by-room list of the windows and doors you want to change. Take photos and note any draughts, condensation or difficult openings. Rough measurements can help an initial discussion; a professional survey is still needed.',
					),
					array(
						'At the survey',
						'Talk through frame styles, colours, glass, ventilation and access. Ask what the quote includes and what work may be needed around the opening.',
					),
					array(
						'Before you commit',
						'Check the agreed specification, project schedule, payment terms, warranty and compliance arrangements in writing. Keep the final documentation somewhere safe.',
					),
				),
			),
		),
		'faqs' => array(
			array(
				'Where are you based?',
				'Splendid Double Glazing is based at 758 Sidcup Road, London, SE9 3NS. Share your postcode and the team can confirm coverage and arrange the next step.',
			),
			array(
				'How do I get a price?',
				'Tell us what you would like to change, the approximate number of windows or doors, and your postcode. The team will discuss your project and confirm the measurements and specification needed for an accurate written quote.',
			),
			array(
				'Can I choose the colour and style?',
				'Yes, there are different materials, colours, glass designs and opening styles to explore. Available options depend on the product you select, so your final choices are confirmed before ordering.',
			),
			array(
				'What guarantee and certification will I receive?',
				'Ask the team to confirm the product warranty, installation cover and applicable compliance documentation in your written quotation. These details can vary by product and project.',
			),
			array(
				'How long will my project take?',
				'Timing depends on the chosen products, manufacturing lead times, access and the work involved. Your installation schedule should be agreed once the survey and specification are complete.',
			),
		),
	);
}
