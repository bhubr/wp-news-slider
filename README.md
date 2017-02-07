# WordPress Simple Post Slider Widget

This is (sigh) yet another plugin that allows you to put a content slider in your sidebar, footer, wherever you want and your theme allows you.

Among its features:
- Full responsive
- Allows to choose a type of content

# Todo

- widget title link => blog (or list of custom post types)
- bug width slide horizontal (on window resize)
- Full translation
- Bug with heights, again ($.outerHeight() gives 30px on a 2-lined h3 which takes up 60px...)
- More clean-up and follow WP Code Conventions (PHP&JS)
- Build (minify CSS&JS) and package (remove unwanted files)
- Test under major WP themes (free ones + Zoo and Jupiter that I own)
- ~~Add a clearfix at the end of the thumb div (actually done in JS since it is where we mess with heights)~~
- ~~Re-enable Twig cache (only in production)~~
- ~~Handle custom post types~~
- ~~Reset timer after number/prev/next click~~
- ~~autoplay on/off option~~
- ~~Vertically align thumbs content after setting all of them to same height (optional)~~
- ~~Slide horizontally or vertically~~
- ~~Bullets instead of numbers (option)~~
- ~~Twig template for options~~
- ~~Plug prev&next~~
- ~~Highlight les boutons numérotés~~
- ~~Replace Previous, Next, Stop, Play with icons (no need for i18n on frontend!)~~
- ~~Prev&Next side-to-side with play&stop~~
- ~~Put back buttons~~
- ~~Allow various instances (use WP_Widget instance's id)~~
- ~~compter le &lt;p&gt; de buttons dans calcul hauteur (plus maintenant !)~~
