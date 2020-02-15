<?php
/* Copyright (C) 2001-2002 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Jean-Louis Bergamo   <jlb@j1b.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *      \file       event/admin/registration_extrafields.php
 *		\ingroup    member
 *		\brief      Page to setup extra fields of registration
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=include("../../../main.inc.php"); // for curstom directory

if (! $res) die("Include of main fails");
require_once("../lib/event.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/member.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/extrafields.class.php");

// Load traductions files required by page
$langs->load("event@event");
$langs->load("admin");

$extrafields = new ExtraFields($db);
$form = new Form($db);

// List of supported format
$tmptype2label=  ExtraFields::$type2label;
$type2label=array('');
foreach ($tmptype2label as $key => $val) $type2label[$key]=$langs->trans($val);

// Get parameters
$action=GETPOST("action");
$attrname=GETPOST('attrname', 'alpha');
$elementtype='event_registration';

// Protection if external user or not an admin
if (!$user->admin) accessforbidden();

/*
 * Actions
 */
require(DOL_DOCUMENT_ROOT."/core/actions_extrafields.inc.php");

/*
 * View
 */

// $textobject=$langs->transnoentitiesnoconv("Registrations");

llxHeader('',$langs->trans("EventsSetup"),$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("EventsSetup"), $linkback, 'title_setup');

$head = event_admin_prepare_head();

dol_fiche_head($head, 'attributes', $langs->trans("Events"), 0, 'event@event');

dol_htmloutput_errors($mesg);

// Load attribute_label
$extrafields->fetch_name_optionals_label($elementtype);

print "<table summary=\"listofattributes\" class=\"noborder\" width=\"100%\">";

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Label").'</td>';
print '<td>'.$langs->trans("AttributeCode").'</td>';
print '<td>'.$langs->trans("Type").'</td>';
print '<td align="right">'.$langs->trans("Size").'</td>';
print '<td align="center">'.$langs->trans("Unique").'</td>';
print '<td align="center">'.$langs->trans("Required").'</td>';
print '<td width="80">&nbsp;</td>';
print "</tr>\n";

$var=True;
foreach($extrafields->attribute_type as $key => $value)
{
    $var=!$var;
    print "<tr ".$bc[$var].">";
    print "<td>".$extrafields->attribute_label[$key]."</td>\n";
    print "<td>".$key."</td>\n";
    print "<td>".$type2label[$extrafields->attribute_type[$key]]."</td>\n";
    print '<td align="right">'.$extrafields->attribute_size[$key]."</td>\n";
    print '<td align="center">'.yn($extrafields->attribute_unique[$key])."</td>\n";
    print '<td align="center">'.yn($extrafields->attribute_required[$key])."</td>\n";
    print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit&attrname='.$key.'">'.img_edit().'</a>';
    print "&nbsp; <a href=\"".$_SERVER["PHP_SELF"]."?action=delete&attrname=$key\">".img_delete()."</a></td>\n";
    print "</tr>";
}

print "</table>";

dol_fiche_end();

/*
 * Barre d'actions
 *
 */
if ($action != 'create' && $action != 'edit')
{
	print '<div class="tabsAction">';
	print "<a class=\"butAction\" href=\"".$_SERVER["PHP_SELF"]."?action=create\">".$langs->trans("NewAttribute")."</a>";
	print "</div>";
}

/* ************************************************************************** */
/*                                                                            */
/* Creation d'un champ optionnel
 /*                                                                            */
/* ************************************************************************** */

if ($action == 'create')
{
	print "<br>";
	print_titre($langs->trans('NewAttribute'));

	require DOL_DOCUMENT_ROOT.'/core/tpl/admin_extrafields_add.tpl.php';
}

/* ************************************************************************** */
/*                                                                            */
/* Edition d'un champ optionnel                                               */
/*                                                                            */
/* ************************************************************************** */
if ($action == 'edit' && ! empty($attrname))
{
	print "<br>";
	print_titre($langs->trans("FieldEdition", $attrname));

	require DOL_DOCUMENT_ROOT.'/core/tpl/admin_extrafields_edit.tpl.php';
}

// End of page
llxFooter();
$db->close();
