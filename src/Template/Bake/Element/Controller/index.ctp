    /**
     * Lists <%= $pluralHumanName %>
     */
    public function index() {
<% $belongsTo = $this->Bake->aliasExtractor($modelObj, 'BelongsTo'); %>
<% if ($belongsTo): %>
        $this->paginate = [
            'contain' => [<%= $this->Bake->stringifyList($belongsTo, ['indent' => false]) %>]
        ];
<% endif; %>
        $this->set('<%= $pluralName %>', $this->paginate($this-><%= $currentModelName %>));
        $this->set('_serialize', ['<%= $pluralName %>']);
    }
	