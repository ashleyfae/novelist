=== Novelist ===
Contributors: NoseGraze
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=L2TL7ZBVUMG9C
Tags: books, authors, goodreads, writing, publishing, writer
Requires at least: 4.0
Tested up to: 6.3.1
Requires PHP: 7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily organize and display your portfolio of books.

== Description ==

Novelist helps authors easily organize and display their portfolio of books.

**Features:**

* All books are stored in their own "Books" area.
* Easy form for filling out book information.
* All book information is automatically formatted for you.
* Customizable templates.
* Easily create an archive of all your books.
* Developer-friendly.

**Available Book Info Fields:**

* Title
* Book Cover
* Series (name and number)
* Contributors
* Publisher
* Release Date
* Genres
* Pages
* Synopsis
* Goodreads Link
* Purchase Links (configure your own list of retail sites)
* ISBN13
* ASIN
* Excerpt
* Extra Text

**Documentation**

Extensive documentation is available at [novelistplugin.com/docs](https://novelistplugin.com/docs/)

== Installation ==

1. Upload `novelist` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Adjust the settings in Books -> Settings

== Frequently Asked Questions ==

= How can I customize the book template? =

To change the text and order of book information, read this article on [customizing the book layout](https://novelistplugin.com/docs/general/plugin-settings/book-layout/).

= How can I change the available purchase link websites? =

The retail sites are configured in Books > Settings in the "Purchase Links" area.

More instructions: [Setting Up Retail Sites](https://novelistplugin.com/docs/general/plugin-settings/retail/)

= How can I change the order of books in the automated archive? =

https://novelistplugin.com/docs/general/faqs/book-order/

= How can I create my own custom grid of books? =

This can be done using the `[novelist-books]` shortcode. For full instructions, read the [[novelist-books] shortcode documentation](https://novelistplugin.com/docs/general/shortcodes/novelist-books/)

= How can I turn the Goodreads link into an image instead of text? =

https://novelistplugin.com/docs/general/faqs/goodreads-image/

= How can I use site logo images instead of plain text for the purchase links? =

https://novelistplugin.com/docs/general/faqs/images-purchase-links/

= How can I create a new custom field for a piece of book information? =

Tutorial: https://novelistplugin.com/docs/developers/tutorials/book-info-field/

= I want greater control over the single book display. How can I do that? =

Create a new folder in your theme folder called `novelist_templates`. Inside that folder, create a new file called `book-content.php`. This file is responsible for displaying all the book information. You can now create more complex layouts with book information. You'll need to use the [Novelist_Book class](https://novelistplugin.com/docs/developers/classes/novelist-book/) to assist you in displaying each piece of book info. For example:

`$book = new Novelist_Book( get_the_ID() );

if ( $book->title ) {
    echo '<h1>' . esc_html( $book->title ) . '</h1>';
}

if ( $book->synopsis ) {
    echo '<blockquote class="novelist-synopsis">' . wpautop( $book->synopsis ) . '</blockquote>';
}`

= How can I disable the taxonomy archive links? =

The Novelist plugin creates two custom taxonomies:

- `novelist-genre` for genres
- `novelist-series` for series

By default, when showing book information, each taxonomy term (genre name or series name) will link to an automatically generated archive page. To disable these links, add this code snippet to a custom plugin:

`add_filter('novelist/taxonomy/series-args', 'agNovelistDisableArchives');
add_filter('novelist/taxonomy/genre-args', 'agNovelistDisableArchives');

function agNovelistDisableArchives($args) {
	$args['public'] = false;

	return $args;
}`

== Screenshots ==

1. Book layout builder.
2. Set up retail sites for purchase links.
3. View all books in the admin panel.
4. Edit Book page.
5. Default single book page with the Twenty Fifteen theme.
6. Demonstration of the `[novelist-books]` shortcode in the Twenty Fifteen theme. Creates a grid of all books.

== Upgrade Notice ==

= 1.2.3 =
Security fix

== Changelog ==

= 1.2.3 - 7 March 2024 =
* Security Fix (CSRF)

= 1.2.2 - 24 September 2023 =
* Fix: Potential fatal error on certain settings pages.

= 1.2.1 - 21 May 2023 =
* Security Fix: Add sanitization to the book info fields to disallow some HTML.

= 1.2.0 - 21 April 2023 =
* New: Made it possible (via custom code) to disable the automatic series and genre archives. See FAQ for instructions.

= 1.1.11 - 6 April 2021 =
* New: Each book's ID number is now displayed on the admin page.
* New: The `[show-book]` shortcode can now accept a book ID instead of the title. Example: `[show-book id="10"]`

= 1.1.10 - 9 January 2021 =
* Fix: Ensure all books are available to be selected in Novelist Book widget dropdown. (If you had more than 200 books, some may not have appeared in the dropdown.)

= 1.1.9 - 11 August 2019 =
* Fix: Add fallback if `array_key_first()` function doesn't exist (PHP versions lower than 7.3).

= 1.1.8 - 7 August 2019 =
* Fix: Fix problem with first "Add-Ons" settings section not saving.

= 1.1.7 =
* Fix: "continue targeting switch is equivalent to break" error with PHP 7.3.

= 1.1.6 =
* New: Added ability to reorder purchase links.

= 1.1.5 =
* Fix old style constructor in the Browser class.
* Updated documentation URLs.

= 1.1.4 =
* Fix name of templates folder in FAQ.
* You can now show a grid of specific books in the [novelist-books] shortcode using the `ids` attribute.
* Fixed undefined index notices if buttons weren't saved yet.

= 1.1.3 =
* Change the way book info is added to the_content, so it should appear at the beginning rather than the end. This should fix an issue with social share links incorrectly being added before book info rather than after.

= 1.1.2 =
* You can now change the retail store names without losing existing links. Just make sure you don't change the "Store ID" (new field in Purchase Links settings).

= 1.1.1 =
* Fixed problem with custom button colours not working.
* `[novelist-books]` - Now passing `$atts` as a parameter to actions.
* `[novelist-books]` - Added an extra attribute for `display`, which is purely handled by an action. This allows third party plugins to easily configure their own extra attributes in a configurable order.

= 1.1.0 =
* New: Added "Default Cover Image" setting in Settings > Book Layout > Settings.
* `[novelist-books]` shortcode: Changed order by publication date argument to use meta_value_num instead of meta_value.
* Tweaked media upload script to allow for displaying specific sizes.
* Tools: You can now choose to export all settings or only the book layout. This is useful for transferring book layouts from one site to another, while leaving the other settings in tact.
* New button above visual editor for aiding in inserting a book grid.
* Changed label font-weights from bold to 600 because bold looked super bold in WP 4.6.
* Prevented the "Book Title" field from defaulting to "Audo Draft" when creating a new book due to previous update.
* Fixed an issue with the book cover showing up blank when first uploading on the Add/Edit Book page.
* "Reset Tab" button now only resets the settings in that specific sub-section within a tab. Also renamed the button to "Reset Section".

= 1.0.5 =
* Added an option to allow add-ons to disable editing of custom fields in the Book Layout.
* Added some actions and filters to purchase link formatting and settings area.
* Added new method to `Novelist_Book` for getting the formatted purchase links.
* Added Novelist class names to `<body>` tag if viewing a Novelist archive/singular page.
* Books on the series taxonomy archive are now listed in order of the series number (ascending).
* Added `wpautop` filter to Excerpt field.
* Adjusted the `[novelist-series-grid]` shortcode so a "No Books Found" error doesn't appear if there are no standalones.
* Fixed an error that appears in the Book Layout builder if a field gets removed but is still stuck in the builder.
* Added new settings field callback for numbers (not in use).
* Added new settings field callback for image dimensions (not in use).
* Modified the `get_title()` method in `Novelist_Book`. If no book title is filled out then it falls back to using the post title.
* Updated language files.

= 1.0.4 =
* You can now enter "none" in the series parameter in the `[novelist-books]` shortcode to only show books that don't have a series.
* Added "offset" parameter to the `[novelist-books]` shortcode. You can use this to exclude the first x number of books from the results. Example: `[novelist-books offset="1"]` would skip the first result and display all the others.
* Added a "series-number" parameter to the `[novelist-books]` shortcode. If set to "true" then the book's series number will be displayed.
* New shortcode: `[novelist-series-grid]`. This shortcode displays the `[novelist-books]` shortcode for every single series. It's like manually using `[novelist-books series="Series Name"]` for every series you have, but this new shortcode does it automatically. More details: https://novelistplugin.com/docs/general/shortcodes/novelist-series-grid/

= 1.0.3 =
* Added CSS for textareas in meta boxes. Also added styles for a few generic wrapper CSS classes.
* Added new Extensions page under "Books".
* Modified the sanitization on 'synopsis', 'excerpt', and 'extra' fields to allow for iframes.
* Added support for old `[books-in]` shortcode (but marked it as deprecated, so don't use it).
* Added database upgrades functionality.
* Added a piece of JS for more generic sortable fields (not necessarily book layouts).
* Added filter for "Settings Reset" values: `novelist/settings/restore-defaults`
* Added JavaScript and CSS to support repeater meta fields (not currently used).
* Added a new widget for displaying books in a series.
* Fixed an issue where books would disappear from the admin "All Books" page if you checked to hide them from archives.
* Shortcodes now work in book templates.

= 1.0.2 =
* Fixed a problem with "Other books in this series" template appearing even if there are no other books in the series.
* Added new functions for future use.
* Added new setting in Book Layout > Settings for specifying the size of the book cover image.

= 1.0.1 - 17 April 2016 =
* Updated get_terms() usage to comply with new 4.5 format.
* Added progress bar CSS for future batch actions.
* Added update_value method to Novelist_Book.
* Tested with WordPress 4.5.

= 1.0.0 - 13 April 2016 =
* Initial release.
