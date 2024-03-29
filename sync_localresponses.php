<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Accept uploading files by web service token to the user draft file area.
 *
 * POST params:
 *  token => the web service user token (needed for authentication)
 *  filepath => file path (where files will be stored)
 *  [_FILES] => for example you can send the files with <input type=file>,
 *              or with curl magic: 'file_1' => '@/path/to/file', or ...
 *  itemid   => The draftid - this can be used to add a list of files
 *              to a draft area in separate requests. If it is 0, a new draftid will be generated.
 *
 * @package    quizaccess_wifiresilience
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * AJAX_SCRIPT - exception will be converted into JSON
 */
define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/webservice/lib.php');

$filepath = optional_param('filepath', '/', PARAM_PATH);
$itemid = optional_param('itemid', 0, PARAM_INT);
echo $OUTPUT->header();

// Authenticate the user.
$token = required_param('token', PARAM_ALPHANUM);
$cmid = required_param('cmid', PARAM_ALPHANUM);

$webservicelib = new webservice();
$authenticationinfo = $webservicelib->authenticate_user($token);
$fileuploaddisabled = empty($authenticationinfo['service']->uploadfiles);

if ($fileuploaddisabled) {
    throw new webservice_access_exception('Web service file upload must be enabled in external service settings');
}

$context = context_module::instance($cmid);
$fs = get_file_storage();
$totalsize = 0;
$files = array();

// Check for upload errors.
foreach ($_FILES as $fieldname => $uploadedfile) {
    if (!empty($_FILES[$fieldname]['error'])) {
        switch ($_FILES[$fieldname]['error']) {
            case UPLOAD_ERR_INI_SIZE:
                throw new moodle_exception('upload_error_ini_size', 'repository_upload');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                throw new moodle_exception('upload_error_form_size', 'repository_upload');
                break;
            case UPLOAD_ERR_PARTIAL:
                throw new moodle_exception('upload_error_partial', 'repository_upload');
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new moodle_exception('upload_error_no_file', 'repository_upload');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new moodle_exception('upload_error_no_tmp_dir', 'repository_upload');
                break;
            case UPLOAD_ERR_CANT_WRITE:
                throw new moodle_exception('upload_error_cant_write', 'repository_upload');
                break;
            case UPLOAD_ERR_EXTENSION:
                throw new moodle_exception('upload_error_extension', 'repository_upload');
                break;
            default:
                throw new moodle_exception('nofile');
        }
    }

    // Scan for viruses.
    \core\antivirus\manager::scan_file($_FILES[$fieldname]['tmp_name'], $_FILES[$fieldname]['name'], true);
    $file = new stdClass();
    $file->filename = clean_param($_FILES[$fieldname]['name'], PARAM_FILE);
    $file->filename = str_replace('-', '_', $file->filename);
    $file->filename = $file->filename.'.sync';

    // Check system maxbytes setting.
    if ($_FILES[$fieldname]['size'] == 0) {
        $file->errortype = 'filezero';
        $file->error = 'Size Zero';
    } else {
        $file->filepath = $_FILES[$fieldname]['tmp_name'];
        $totalsize += $_FILES[$fieldname]['size'];
    }

    $files[] = $file;
}

$fs = get_file_storage();
if ($itemid <= 0) {
    $itemid = file_get_unused_draft_itemid();
}
$results = array();

foreach ($files as $file) {
    if (!empty($file->error)) {
        $results[] = $file;
        continue;
    }

    $filerecord = new stdClass;
    $filerecord->component = 'quizaccess_wifiresilience';
    $filerecord->contextid = $context->id;
    $filerecord->userid    = $USER->id;
    $filerecord->filearea  = 'synced_exam_files';
    $filerecord->filename  = $file->filename;
    $filerecord->filepath  = $filepath;
    $filerecord->itemid    = $itemid;
    $filerecord->license   = $CFG->sitedefaultlicense;
    $filerecord->author    = fullname($authenticationinfo['user']);
    $filerecord->source    = serialize((object)array('source' => $file->filename));

    // Check if the file already exists.
    $existingfile = $fs->file_exists(
        $filerecord->contextid,
        $filerecord->component,
        $filerecord->filearea,
        $filerecord->itemid,
        $filerecord->filepath,
        $filerecord->filename);

    if ($existingfile) {
        $newfilename = $fs->get_unused_filename(
            $filerecord->contextid,
            $filerecord->component,
            $filerecord->filearea,
            $filerecord->itemid,
            $filerecord->filepath,
            $filerecord->filename);

        if ($newfilename) {
            $filerecord->filename = $newfilename;
        }
    }

    $storedfile = $fs->create_file_from_pathname($filerecord, $file->filepath);
    $results[] = $filerecord;
}
echo json_encode($results);
