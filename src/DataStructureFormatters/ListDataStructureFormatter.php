<?php
namespace PoP\Engine\DataStructureFormatters;

use PoP\ComponentModel\DataStructure\AbstractDataStructureFormatter;

class ListDataStructureFormatter extends AbstractDataStructureFormatter
{
    public const NAME = 'list';
    public static function getName()
    {
        return self::NAME;
    }

    public function getJsonEncodeType()
    {
        return JSON_FORCE_OBJECT;
    }

    protected function addDBEntries(&$database, &$merged_databases)
    {
        foreach ($database as $db_key => $dbobject) {
            foreach ($dbobject as $dbobject_id => $dbobject_data) {
                $merged_databases[$db_key][$dbobject_id] = array_merge(
                    $merged_databases[$db_key][$dbobject_id] ?? array(),
                    $dbobject_data
                );
            }
        }
    }

    public function getFormattedData($data)
    {

        // If we are requesting only the databases, then return these as a list of items
        $vars = \PoP\ComponentModel\Engine_Vars::getVars();
        $dataoutputitems = $vars['dataoutputitems'];
        if (in_array(GD_URLPARAM_DATAOUTPUTITEMS_DATABASES, $dataoutputitems)) {
            $ret = array();

            // If there are no "databases" entry, then there are no results, so return an empty array
            if ($databases = $data['databases']) {
                // First pass: merge all content about the same DB object
                // Eg: notifications can appear under "database" and "userstatedatabase", showing different fields on each
                $merged_databases = array();
                $dboutputmode = $vars['dboutputmode'];
                if ($dboutputmode == GD_URLPARAM_DATABASESOUTPUTMODE_SPLITBYDATABASES) {
                    foreach ($databases as $database_name => $database) {
                        $this->addDBEntries($database, $merged_databases);
                    }
                } elseif ($dboutputmode == GD_URLPARAM_DATABASESOUTPUTMODE_COMBINED) {
                    $this->addDBEntries($databases, $merged_databases);
                }

                // Second pass: extract all items, and return it as a list
                // Watch out! It doesn't make sense to mix items from 2 or more db_keys (eg: posts and users),
                // so this formatter should be used only when displaying data from a unique one (eg: only posts)
                foreach ($merged_databases as $db_key => $dbobject) {
                    $ret = array_merge(
                        $ret,
                        array_values($dbobject)
                    );
                }
            }

            return $ret;
        }

        return parent::getFormattedData($data);
    }
}
