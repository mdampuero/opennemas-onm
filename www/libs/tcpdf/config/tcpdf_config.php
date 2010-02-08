<?php
if (!defined("K_TCPDF_EXTERNAL_CONFIG")) {
define ("K_PATH_MAIN", dirname(__FILE__)."/../../tcpdf/");
define ("K_PATH_URL", URL); //"http://localhost/newspaper/libs/tcpdf/");
define ("FPDF_FONTPATH", K_PATH_MAIN."fonts/");
define ("K_PATH_CACHE", K_PATH_MAIN."cache/");
define ("K_PATH_URL_CACHE", K_PATH_URL."themes/default/cache/"); // ¿?¿?¿?
define ("K_PATH_IMAGES", dirname(__FILE__).'/../../../media/');
define ("K_BLANK_IMAGE", K_PATH_IMAGES."_blank.png");
define ("PDF_PAGE_FORMAT", "A4");
define ("PDF_PAGE_ORIENTATION", "P");
define ("PDF_CREATOR", "TCPDF");
define ("PDF_AUTHOR", "TCPDF");
define ("PDF_HEADER_TITLE", "");
define ("PDF_HEADER_STRING", "    Boletín - Crónicas de la Emigración");
define ("PDF_HEADER_LOGO", "logo.jpg");
define ("PDF_HEADER_LOGO_WIDTH", 60);
define ("PDF_UNIT", "mm");
define ("PDF_MARGIN_HEADER", 5);
define ("PDF_MARGIN_FOOTER", 10);
define ("PDF_MARGIN_TOP", 27);
define ("PDF_MARGIN_BOTTOM", 25);
define ("PDF_MARGIN_LEFT", 15);
define ("PDF_MARGIN_RIGHT", 15);
define ("PDF_FONT_NAME_MAIN", "vera"); //vera
define ("PDF_FONT_SIZE_MAIN", 10);
define ("PDF_FONT_NAME_DATA", "vera"); //verase
define ("PDF_FONT_SIZE_DATA", 8);
define ("PDF_IMAGE_SCALE_RATIO", 4);
define("HEAD_MAGNIFICATION", 1.1);
define("K_CELL_HEIGHT_RATIO", 1.25);
define("K_TITLE_MAGNIFICATION", 1.3);
define("K_SMALL_RATIO", 2/3);
}