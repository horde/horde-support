<?php
/**
 * Ckeditor copy script - copies files used for Horde.
 *
 * Usage: horde-ckeditor.php [source] [destination]
 *
 * Copyright 1999-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @package  maintainer_tools
 */

/* File list. */
$files = array(
    'LICENSE.html',
    'ckeditor.js',
    'ckeditor_basic.js',
    'LICENSE.html',
    'ckeditor.js',
    'ckeditor_basic.js',
    'config.js',
    'contents.css',
    'images/spacer.gif',
    'lang/_languages.js',
    'lang/af.js',
    'lang/ar.js',
    'lang/bg.js',
    'lang/bn.js',
    'lang/bs.js',
    'lang/ca.js',
    'lang/cs.js',
    'lang/cy.js',
    'lang/da.js',
    'lang/de.js',
    'lang/el.js',
    'lang/en-au.js',
    'lang/en-ca.js',
    'lang/en-gb.js',
    'lang/en-uk.js',
    'lang/en.js',
    'lang/eo.js',
    'lang/es.js',
    'lang/et.js',
    'lang/eu.js',
    'lang/fa.js',
    'lang/fi.js',
    'lang/fo.js',
    'lang/fr-ca.js',
    'lang/fr.js',
    'lang/gl.js',
    'lang/gu.js',
    'lang/he.js',
    'lang/hi.js',
    'lang/hr.js',
    'lang/hu.js',
    'lang/is.js',
    'lang/it.js',
    'lang/ja.js',
    'lang/km.js',
    'lang/ko.js',
    'lang/lt.js',
    'lang/lv.js',
    'lang/mn.js',
    'lang/ms.js',
    'lang/nb.js',
    'lang/nl.js',
    'lang/no.js',
    'lang/pl.js',
    'lang/pt-br.js',
    'lang/pt.js',
    'lang/ro.js',
    'lang/ru.js',
    'lang/sk.js',
    'lang/sl.js',
    'lang/sr-latn.js',
    'lang/sr.js',
    'lang/sv.js',
    'lang/th.js',
    'lang/tr.js',
    'lang/uk.js',
    'lang/vi.js',
    'lang/zh-cn.js',
    'lang/zh.js',
    'plugins/a11yhelp/dialogs/a11yhelp.js',
    'plugins/a11yhelp/lang/en.js',
    'plugins/a11yhelp/lang/he.js',
    'plugins/about/dialogs/about.js',
    'plugins/about/dialogs/logo_ckeditor.png',
    'plugins/clipboard/dialogs/paste.js',
    'plugins/colordialog/dialogs/colordialog.js',
    'plugins/dialog/dialogDefinition.js',
    'plugins/div/dialogs/div.js',
    'plugins/find/dialogs/find.js',
    'plugins/flash/dialogs/flash.js',
    'plugins/flash/images/placeholder.png',
    'plugins/forms/dialogs/button.js',
    'plugins/forms/dialogs/checkbox.js',
    'plugins/forms/dialogs/form.js',
    'plugins/forms/dialogs/hiddenfield.js',
    'plugins/forms/dialogs/radio.js',
    'plugins/forms/dialogs/select.js',
    'plugins/forms/dialogs/textarea.js',
    'plugins/forms/dialogs/textfield.js',
    'plugins/forms/images/hiddenfield.gif',
    'plugins/iframedialog/plugin.js',
    'plugins/image/dialogs/image.js',
    'plugins/link/dialogs/anchor.js',
    'plugins/link/dialogs/link.js',
    'plugins/link/images/anchor.gif',
    'plugins/liststyle/dialogs/liststyle.js',
    'plugins/liststyle/plugin.js',
    'plugins/pagebreak/images/pagebreak.gif',
    'plugins/pastefromword/filter/default.js',
    'plugins/pastetext/dialogs/pastetext.js',
    'plugins/scayt/dialogs/options.js',
    'plugins/scayt/dialogs/toolbar.css',
    'plugins/showblocks/images/block_address.png',
    'plugins/showblocks/images/block_blockquote.png',
    'plugins/showblocks/images/block_div.png',
    'plugins/showblocks/images/block_h1.png',
    'plugins/showblocks/images/block_h2.png',
    'plugins/showblocks/images/block_h3.png',
    'plugins/showblocks/images/block_h4.png',
    'plugins/showblocks/images/block_h5.png',
    'plugins/showblocks/images/block_h6.png',
    'plugins/showblocks/images/block_p.png',
    'plugins/showblocks/images/block_pre.png',
    'plugins/smiley/dialogs/smiley.js',
    'plugins/smiley/images/angel_smile.gif',
    'plugins/smiley/images/angry_smile.gif',
    'plugins/smiley/images/broken_heart.gif',
    'plugins/smiley/images/confused_smile.gif',
    'plugins/smiley/images/cry_smile.gif',
    'plugins/smiley/images/devil_smile.gif',
    'plugins/smiley/images/embaressed_smile.gif',
    'plugins/smiley/images/envelope.gif',
    'plugins/smiley/images/heart.gif',
    'plugins/smiley/images/kiss.gif',
    'plugins/smiley/images/lightbulb.gif',
    'plugins/smiley/images/omg_smile.gif',
    'plugins/smiley/images/regular_smile.gif',
    'plugins/smiley/images/sad_smile.gif',
    'plugins/smiley/images/shades_smile.gif',
    'plugins/smiley/images/teeth_smile.gif',
    'plugins/smiley/images/thumbs_down.gif',
    'plugins/smiley/images/thumbs_up.gif',
    'plugins/smiley/images/tounge_smile.gif',
    'plugins/smiley/images/whatchutalkingabout_smile.gif',
    'plugins/smiley/images/wink_smile.gif',
    'plugins/specialchar/dialogs/specialchar.js',
    'plugins/styles/styles/default.js',
    'plugins/stylescombo/styles/default.js',
    'plugins/table/dialogs/table.js',
    'plugins/tabletools/dialogs/tableCell.js',
    'plugins/templates/dialogs/templates.js',
    'plugins/templates/templates/default.js',
    'plugins/templates/templates/images/template1.gif',
    'plugins/templates/templates/images/template2.gif',
    'plugins/templates/templates/images/template3.gif',
    'plugins/uicolor/dialogs/uicolor.js',
    'plugins/uicolor/lang/en.js',
    'plugins/uicolor/plugin.js',
    'plugins/uicolor/uicolor.gif',
    'plugins/uicolor/yui/assets/hue_bg.png',
    'plugins/uicolor/yui/assets/hue_thumb.png',
    'plugins/uicolor/yui/assets/picker_mask.png',
    'plugins/uicolor/yui/assets/picker_thumb.png',
    'plugins/uicolor/yui/assets/yui.css',
    'plugins/uicolor/yui/yui.js',
    'plugins/wsc/dialogs/ciframe.html',
    'plugins/wsc/dialogs/tmpFrameset.html',
    'plugins/wsc/dialogs/wsc.css',
    'plugins/wsc/dialogs/wsc.js',
    'skins/kama/dialog.css',
    'skins/kama/editor.css',
    'skins/kama/icons.png',
    'skins/kama/images/dialog_sides.gif',
    'skins/kama/images/dialog_sides.png',
    'skins/kama/images/dialog_sides_rtl.png',
    'skins/kama/images/mini.gif',
    'skins/kama/images/noimage.png',
    'skins/kama/images/sprites.png',
    'skins/kama/images/sprites_ie6.png',
    'skins/kama/images/toolbar_start.gif',
    'skins/kama/skin.js',
    'skins/kama/templates.css',
    'skins/office2003/dialog.css',
    'skins/office2003/editor.css',
    'skins/office2003/icons.png',
    'skins/office2003/images/dialog_sides.gif',
    'skins/office2003/images/dialog_sides.png',
    'skins/office2003/images/dialog_sides_rtl.png',
    'skins/office2003/images/mini.gif',
    'skins/office2003/images/noimage.png',
    'skins/office2003/images/sprites.png',
    'skins/office2003/images/sprites_ie6.png',
    'skins/office2003/skin.js',
    'skins/office2003/templates.css',
    'skins/v2/dialog.css',
    'skins/v2/editor.css',
    'skins/v2/icons.png',
    'skins/v2/images/dialog_sides.gif',
    'skins/v2/images/dialog_sides.png',
    'skins/v2/images/dialog_sides_rtl.png',
    'skins/v2/images/mini.gif',
    'skins/v2/images/noimage.png',
    'skins/v2/images/sprites.png',
    'skins/v2/images/sprites_ie6.png',
    'skins/v2/images/toolbar_start.gif',
    'skins/v2/skin.js',
    'skins/v2/templates.css',
    'themes/default/theme.js'
);

/* Ignore these directories. */
$ignore = array(
    '_samples',
    '_source'
);

/* Strip BOM/Dos linebreaks for these extensions. */
$strip = array(
    'css',
    'html',
    'js'
);

$source = rtrim($argv[1], '/ ');
$dest = rtrim($argv[2], '/ ');

$not_copy = array();

$di = new RecursiveIteratorIterator(new IgnoreFilterIterator(new RecursiveDirectoryIterator($source)), RecursiveIteratorIterator::SELF_FIRST);
foreach ($di as $val) {
    if ($val->isFile()) {
        $pathname = $di->getSubPathname();

        if (in_array($pathname, $files)) {
            $ext = pathinfo($val->getFileName(), PATHINFO_EXTENSION);

            if (!file_exists($dest . '/' . $di->getSubPath())) {
                @mkdir($dest . '/' . $di->getSubPath(), 0777, true);
            }

            $data = file_get_contents($val->getPathname());

            // Remove BOM & DOS linebreaks
            if (in_array($ext, $strip)) {
                $data = preg_replace(array("/^\xEF\xBB\xBF/", "/\r\n/"), array('', "\n"), $data);
            }

            file_put_contents($dest . '/' . $pathname, $data);

            switch ($ext) {
            case 'js':
                /* Compress javascript files. */
                system('php ' . dirname(__FILE__) . '/horde-js-compress.php --nojsmin --overwrite ' . escapeshellcmd($dest . '/' . $pathname));
                break;
            }
        } else {
            $not_copy[] = $pathname;
        }
    }
}

print "\nNot copied:\n" .
      "===========\n" .
      implode("\n", $not_copy) .
      "\n";

/* List of tasks that need to be manually done. */
print "\nTODO:\n" .
      "=====\n" .
      "1. Edit config.js file (disable spell check as you type)\n".
      "2. Compress PNGs\n";

class IgnoreFilterIterator extends RecursiveFilterIterator
{
    public function accept()
    {
        return !in_array($this->getInnerIterator()->getSubPath(), $GLOBALS['ignore']);
    }
}