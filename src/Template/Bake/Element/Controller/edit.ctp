<%
$belongsTo = $this->Bake->aliasExtractor($modelObj, 'BelongsTo');
$belongsToMany = $this->Bake->aliasExtractor($modelObj, 'BelongsToMany');
$compact = ["'" . $singularName . "'"];
%>

    /**
     * Edits <%= strtolower($singularHumanName) %>
     * @param string $id <%= $singularHumanName %> id
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null)  {
        $<%= $singularName %> = $this-><%= $currentModelName %>->get($id, [
            'contain' => [<%= $this->Bake->stringifyList($belongsToMany, ['indent' => false]) %>]
        ]);
		
        if($this->request->is(['patch', 'post', 'put'])) {
            $<%= $singularName %> = $this-><%= $currentModelName %>->patchEntity($<%= $singularName %>, $this->request->data);
			
            if($this-><%= $currentModelName; %>->save($<%= $singularName %>)) {
                $this->Flash->success('The <%= strtolower($singularHumanName) %> has been saved');
                return $this->redirect(['action' => 'index']);
            } 
			else
                $this->Flash->error('The <%= strtolower($singularHumanName) %> could not be saved. Please, try again');
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
        $this->set('_serialize', ['<%=$singularName%>']);
    }
