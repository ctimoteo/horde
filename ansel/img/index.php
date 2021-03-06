<?php
/**
 * Copyright 2003-2015 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @author Chuck Hagenbuch <chuck@horde.org>
 */

require_once __DIR__ . '/../lib/Application.php';
Horde_Registry::appInit('ansel');

$image = $GLOBALS['injector']->getInstance('Ansel_Storage')->getImage(Horde_Util::getFormData('image'));
$gallery = $GLOBALS['injector']->getInstance('Ansel_Storage')->getGallery($image->gallery);
if (!$gallery->hasPermission($registry->getAuth(), Horde_Perms::READ)) {
    throw new Horde_Exception_PermissionDenied(_("Access denied viewing this photo."));
}

/* Sendfile support. Lighttpd < 1.5 only understands the X-LIGHTTPD-send-file header */
if ($conf['vfs']['src'] == 'sendfile') {
    $filename = $injector->getInstance('Horde_Core_Factory_Vfs')->create('images')->readFile($image->getVFSPath('screen'), $image->getVFSName('screen'));
    Ansel::doSendFile($filename, $image->getType('screen'));
    exit;
}

$image->display('screen');
