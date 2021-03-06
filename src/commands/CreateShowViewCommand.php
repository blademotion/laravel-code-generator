<?php

namespace CrestApps\CodeGenerator\Commands;

use CrestApps\CodeGenerator\Support\ViewsCommand;
use CrestApps\CodeGenerator\Support\GenerateFormViews;

class CreateShowViewCommand extends ViewsCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:show-view
                            {model-name : The model name that this view will represent.}
                            {--fields= : The fields to define the model.}
                            {--fields-file= : File name to import fields from.}
                            {--views-directory= : The name of the directory to create the views under.}
                            {--routes-prefix= : The routes prefix.}
                            {--layout-name=layouts.app : This will extract the validation into a request form class.}
                            {--template-name= : The template name to use when generating the code.}
                            {--force : This option will override the view if one already exists.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit views for the model.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->stubName = 'show.blade';
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    protected function handleCreateView()
    {
        $input = $this->getCommandInput();
        $stub = $this->getStubContent($this->stubName);
        $fields = $this->getFields($input->fields,$input->languageFileName, $input->fieldsFile);
        $htmlCreator = new GenerateFormViews($fields, $input->modelName);

        $destenationFile = $this->getDestinationViewFullname($input->viewsDirectory, $input->prefix, 'show');
        
        $this->handleNewFilePolicy($destenationFile, $input->force, $fields)
             ->replaceCommonTemplates($stub, $input)
             ->replacePrimaryKey($stub, $this->getPrimaryKeyName($fields))
             ->replaceTableRows($stub, $htmlCreator->getShowRowsHtmlField())
             ->createViewFile($stub, $destenationFile)
             ->info('Show view was created successfully.');

    }

    /**
     * Replaces the table rows for the giving stub
     *
     * @param string $stub
     * @param string $tableHtmlRows
     *
     * @return $this
     */
    protected function replaceTableRows(&$stub, $tableHtmlRows)
    {
        $stub = str_replace('{{tableRows}}', $tableHtmlRows, $stub);

        return $this;
    }

}
