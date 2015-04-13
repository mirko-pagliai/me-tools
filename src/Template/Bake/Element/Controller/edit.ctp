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
$belongsTo = $this->Bake->aliasExtractor($modelObj, 'BelongsTo');
$belongsToMany = $this->Bake->aliasExtractor($modelObj, 'BelongsToMany');
$compact = ["'" . $singularName . "'"];
%>

    /**
     * Edits <%= strtolower($singularHumanName) %>
     * @param string $id <%= $singularHumanName %> ID
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = NULL)  {
        $<%= $singularName %> = $this-><%= $currentModelName %>->get($id, [
            'contain' => [<%= $this->Bake->stringifyList($belongsToMany, ['indent' => false]) %>]
        ]);
		
        if($this->request->is(['patch', 'post', 'put'])) {
            $<%= $singularName %> = $this-><%= $currentModelName %>->patchEntity($<%= $singularName %>, $this->request->data);
			
            if($this-><%= $currentModelName; %>->save($<%= $singularName %>)) {
                $this->Flash->success(__('The <%= strtolower($singularHumanName) %> has been saved'));
                return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error(__('The <%= strtolower($singularHumanName) %> could not be saved. Please, try again'));
        }
<%
        foreach (array_merge($belongsTo, $belongsToMany) as $assoc):
            $association = $modelObj->association($assoc);
            $otherName = $association->target()->alias();
            $otherPlural = $this->_variableName($otherName);
%>
        $<%= $otherPlural %> = $this-><%= $currentModelName %>-><%= $otherName %>->find('list', ['limit' => 200]);
<%
            $compact[] = "'$otherPlural'";
        endforeach;
%>

        $this->set(compact(<%= join(', ', $compact) %>));
    }
