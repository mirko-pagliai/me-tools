<%
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
$allAssociations = array_merge(
    $this->Bake->aliasExtractor($modelObj, 'BelongsTo'),
    $this->Bake->aliasExtractor($modelObj, 'BelongsToMany'),
    $this->Bake->aliasExtractor($modelObj, 'HasOne'),
    $this->Bake->aliasExtractor($modelObj, 'HasMany')
);
%>

    /**
     * Views <%= strtolower($singularHumanName) %>
     * @param string $id <%= $singularHumanName %> ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($id = NULL) {
        $<%= $singularName%> = $this-><%= $currentModelName %>->get($id, [
            'contain' => [<%= $this->Bake->stringifyList($allAssociations, ['indent' => false]) %>]
        ]);
		
        $this->set('<%= $singularName %>', $<%= $singularName %>);
    }
