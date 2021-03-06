<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace Espo\Core\Utils\Database\DBAL\Schema;

use Doctrine\DBAL\Schema\Index as DBALIndex;

class Index extends \Doctrine\DBAL\Schema\Index
{
    public function addFlag($flag)
    {
        $this->_flags[strtolower($flag)] = true;

        return $this;
    }

    public function hasFlag($flag)
    {
        return isset($this->_flags[strtolower($flag)]);
    }

    public function removeFlag($flag)
    {
        unset($this->_flags[strtolower($flag)]);
    }

    public function isFullfilledBy(DBALIndex $other)
    {
        if (count($other->getColumns()) != count($this->getColumns())) {
            return false;
        }

        $sameColumns = $this->spansColumns($other->getColumns());

        if ($sameColumns) {
            $flags = $this->getFlags();
            $otherFlags = $other->getFlags();

            if ( ! $this->isUnique() && !$this->isPrimary() && $flags === $otherFlags) {
                return true;
            } else if ($other->isPrimary() != $this->isPrimary()) {
                return false;
            } else if ($other->isUnique() != $this->isUnique()) {
                return false;
            }

            if (count($flags) != count($otherFlags) || array_diff($flags, $otherFlags) !== array_diff($otherFlags, $flags)) {
                return false;
            }

            return true;
        }

        return false;
    }
}