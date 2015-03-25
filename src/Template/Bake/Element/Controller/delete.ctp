
    /**
     * Deletes <%= strtolower($singularHumanName) %>
     * @param string $id <%= $singularHumanName %> id
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
		
        $<%= $singularName %> = $this-><%= $currentModelName %>->get($id);
		
        if($this-><%= $currentModelName; %>->delete($<%= $singularName %>))
            $this->Flash->success('The <%= strtolower($singularHumanName) %> has been deleted');
        else
            $this->Flash->error('The <%= strtolower($singularHumanName) %> could not be deleted. Please, try again');
			
        return $this->redirect(['action' => 'index']);
    }
