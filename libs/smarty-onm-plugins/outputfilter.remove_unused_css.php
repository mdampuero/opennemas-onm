<?php
/**
 * Handles all the css includes and print them into .
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_outputfilter_remove_unused_css($output, $smarty)
{
    $removeUnusedCss = new \Momentum81\PhpRemoveUnusedCss\RemoveUnusedCssBasic();
    $template        = $smarty->getValue('_template');
    $cssFileName     = $template->getThemeSkinProperty('css_file');
    $themePath       = $smarty->getTheme()->path;

    // Get the current css theme file path
    $originalCssFilePath = $_SERVER['DOCUMENT_ROOT'] . $themePath . 'css/' . $cssFileName;

    // Put the the html content in a .html file
    $newHtmlFilePath      = sys_get_temp_dir() . '/html-aux.html';
    $newCssFilePath       = sys_get_temp_dir() . '/css-aux.css';
    $optimizedCssFilePath = str_replace('.css', '.refactored.min.css', $newCssFilePath);

    file_put_contents($newHtmlFilePath, $output);
    file_put_contents($newCssFilePath, file_get_contents($originalCssFilePath));

    // Use the removeUnusedCss library to get the CSS optimized file
    $removeUnusedCss->styleSheets($newCssFilePath)
        ->htmlFiles($newHtmlFilePath)
        ->setFilenameSuffix('.refactored.min')
        ->minify()
        ->refactor()
        ->saveFiles();

    // Get the CSS code from the optimized CSS file
    $optimizedCss = file_get_contents($optimizedCssFilePath);

    // Replace the style tag with the CSS code of the new optimized CSS file
    $output = preg_replace(
        '@(<link(?![^>]*libraries).*id="theme-css".*?>)@',
        '<style>' . $optimizedCss . '</style>',
        $output
    );

    return $output;
}
