<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Admin\Commands;

use \Qwizi\Core\Admin\Action;
use \Qwizi\Core\Admin\Table;
use \Qwizi\Core\Admin\Pagination;
use \PopupMenu;
use \MyBB;

class ViewAction extends Action
{
    public function __construct($module_link) {
        parent::__construct($module_link);
    }

    public function post() {}

    public function get() {
        global $mybb, $lang, $db;
        $table = new Table('Manage commands');
        $table
            ->header('Name')
            ->header('Description')
            ->header('Active')
            ->header('Options');
        
        $page = $mybb->get_input('page', MyBB::INPUT_INT);

        $pagination = new Pagination(15, $page, $this->module_link);
    
        $numQuery = $db->simple_select("dvz_shoutbox_bot_commands", "COUNT(*) AS num_commands", "");
        $numRequest = $db->fetch_field($numQuery, 'num_commands');
    
        $pagination->setQuery($numQuery);
        $pagination->setNumRequest($numRequest);
        $pagination->countPages();
        $start = $pagination->getStart();
        $perPage = $pagination->getPerPageNum();

        $query = $db->query("
            SELECT c.cid, c.name, c.description, c.activated
            FROM ".TABLE_PREFIX."dvz_shoutbox_bot_commands c
            ORDER BY c.cid ASC
            LIMIT {$start}, {$perPage}
        ");
        while($row = $db->fetch_array($query)) {
            $cid = intval($row['cid']);
            $name = \htmlspecialchars_uni($row['name']);
            $name = sprintf("<a href=\"%s&amp;action=edit&amp;cid=%d\">%s</a>", $this->module_link, $cid, $name);
            $description = \htmlspecialchars_uni($row['description']);
            $activated = \htmlspecialchars_uni($row['activated']);
            $activated = intval($activated);
            
            $popup = new PopupMenu("command_{$cid}", 'Opcje');
            $popup->add_item('Edytuj', $this->module_link."&amp;action=edit&amp;cid={$cid}");

            $table->cells([
                [$name],
                [$description],
                [$activated],
                [$popup->fetch()]
            ]);
        }
        $this->addTable($table);
        $this->addPagination($pagination);
    }
}
