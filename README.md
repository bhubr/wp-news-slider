# WordPress Simple Post Slider Widget

This is (sigh) yet another plugin that allows you to put a content slider in your sidebar, footer, wherever you want and your theme allows you.

Among its features:
- Full responsive
- Allows to choose a type of content

# Todo

- Bug with heights, again ($.outerHeight() gives 30px on a 2-lined h3 which takes up 60px...)
- Slide horizontally or vertically
- widget title => blog (or list of custom post types)
- Add option: "turn post title into link..."
- Vertically align thumbs content after setting all of them to same height (optional)
- Reset timer after number/prev/next click
- Add a clearfix at the end of the thumb div
- Handle custom post types
- Full translation
- More clean-up
- WP Code Conventions (PHP&JS)
- Build (minify CSS&JS) and package (remove unwanted files)
- Re-enable Twig cache
- Test under major WP themes (free ones + Zoo and Jupiter that I own)
- ~~Bullets instead of numbers (option)~~
- ~~Twig template for options~~
- ~~Plug prev&next~~
- ~~Highlight les boutons numérotés~~
- ~~Replace Previous, Next, Stop, Play with icons (no need for i18n on frontend!)~~
- ~~Prev&Next side-to-side with play&stop~~
- ~~Put back buttons~~
- ~~Allow various instances (use WP_Widget instance's id)~~
- ~~compter le <p> de buttons dans calcul hauteur !~~
