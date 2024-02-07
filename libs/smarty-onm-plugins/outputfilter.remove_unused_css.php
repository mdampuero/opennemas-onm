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
    if (!in_array($smarty->getTheme()->text_domain, ['apolo']) || stripos($output, '<!doctype html>') !== 0) {
        return $output;
    }

    $request = $smarty->getContainer()
        ->get('request_stack')
        ->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (preg_match('/newsletter/', $smarty->source->resource)
        && preg_match('/\/manager/', $uri)
        && preg_match('/\/managerws/', $uri)
        && preg_match('/\/sharrre/', $uri)
        && preg_match('/\/ads\//', $uri)
        && preg_match('/\/admin/', $uri)
        && preg_match('/\/comments\//', $uri)
        && preg_match('/\/rss\/(?!listado$)/', $uri)
        && preg_match('@\.amp\.html@', $uri)
    ) {
        return $output;
    }

    $removeUnusedCss = new \Momentum81\PhpRemoveUnusedCss\RemoveUnusedCssBasic();
    $template        = $smarty->getValue('_template');
    $cssFileName     = $template->getThemeSkinProperty('css_file');
    $themePath       = $smarty->getTheme()->path;

    $resource  = str_replace('.css', '.' . THEMES_DEPLOYED_AT . '.css', $cssFileName);
    $stylePath = $smarty->getTheme()->path . 'css' . DS . $resource;

    if (!preg_match('@(<link[^>]*href="' . $stylePath . '".*?>)@', $output)) {
        return $output;
    }
    // Get the current css theme file path
    $originalCssFilePath = $_SERVER['DOCUMENT_ROOT'] . $themePath . 'css/' . $cssFileName;

    // Put the the html and css content in a file
    $newHtmlFilePath = sys_get_temp_dir() . '/html-aux.html';
    $newCssFilePath  = sys_get_temp_dir() . '/css-aux.css';

    file_put_contents($newHtmlFilePath, $output);
    file_put_contents($newCssFilePath, file_get_contents($originalCssFilePath));

    // Use the removeUnusedCss library to get the CSS optimized file
    $removeUnusedCss->styleSheets($newCssFilePath)
        ->htmlFiles($newHtmlFilePath)
        ->setFilenameSuffix('.refactored.min')
        ->alwaysInclude('onm-new')
        ->minify()
        ->refactor()
        ->saveFiles();

    // Get the CSS code from the optimized CSS file
    $optimizedCssFilePath = str_replace('.css', '.refactored.min.css', $newCssFilePath);
    $optimizedCss         = file_get_contents($optimizedCssFilePath);

    // Replace the style tag with the CSS code of the new optimized CSS file
    $output = preg_replace(
        '@(<link[^>]*href="' . $stylePath . '".*?>)@',
        '<style id="optimized-css">' . $optimizedCss . '</style>',
        $output
    );

    if (file_exists($newHtmlFilePath)) {
        unlink($newHtmlFilePath);
    }

    if (file_exists($newCssFilePath)) {
        unlink($newCssFilePath);
    }

    return $output;
}
