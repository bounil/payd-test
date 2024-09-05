<?php
namespace App\Filament\Pages;
 
use App\Models\Country;
use App\Models\Field;
use App\Models\FormSubmission;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use App\Models\Form as FormModel;
use Filament\Http\Responses\Auth\RegistrationResponse;
use Filament\Pages\Auth\Register;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
 
class Registration extends Register
{
    public $country_id;
    public $name;
    public $email;
    public $ID;
    public $IBAN;
    public $formId =1;
    public $image;
    public $uploadedFiles = [];
    protected ?string $maxWidth = '2xl';
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('country')
                        ->schema([
                            
                        Select::make('country_id')
                        ->label('Select Country')
                        ->options(
                            Country::has('forms')->pluck('name', 'id')
                        )
                        ->reactive()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            // Find the form based on the selected country_id
                            $this->formId = FormModel::where('country_id', $get('country_id'))->value('id');
                            
                            // Set form_id in form state
                            $set('form_id', $this->formId);

                            // Debugging log
                            if (!$this->formId) {
                                logger()->info('No form found for the selected country.');
                            }
                        })
                        ->required(), // Mark the field as required
                        ]),
                    Wizard\Step::make('General')
                    ->schema($this->getFieldsByCategory('general')),

                    Wizard\Step::make('Identity')
                          ->schema($this->getFieldsByCategory('identity')),

                    Wizard\Step::make('Bank')
                     ->schema($this->getFieldsByCategory('bank')),

                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        size="sm"
                        wire:submit="register"
                    >
                        Register
                    </x-filament::button>
                    BLADE))),
            ]);
    }
  public function register(): ?RegistrationResponse
    {

        $data = $this->form->getState();


    if (isset($data['identity_document']) && $data['identity_document'] instanceof \Illuminate\Http\UploadedFile) {
      
        $identityDocumentPath = $data['identity_document']->store('uploads/', 'public');
        $data['identity_document'] = $identityDocumentPath;  // Store the file path in the data array
    }
        
        FormSubmission::create([
            'form_id' =>$this->formId,
            'data' => json_encode($data),  // Save form data as JSON
        ]);
        return app(RegistrationResponse::class);
    }
    protected function getFieldsByCategory(string $category): array
    {


        return Field::where('category', $category)->where('form_id', $this->formId)
            ->get()
            ->map(function ($field) {
                return $this->mapFieldToFormComponent($field);
            })
            ->toArray();
    }

    // This method maps the field type to the correct Filament form component
    protected function mapFieldToFormComponent(Field $field)
    {
        switch ($field->type) {
            case 'text':
                return TextInput::make($field->name)
                    ->label(ucfirst($field->name))
                    ->required($field->is_required)
                    ->rules([
                        'required_if:is_required,true',  
                        'string',                        
                        'min:3',
                        'max:255'
                    ]);
            case 'number':
                return TextInput::make($field->name)
                    ->label(ucfirst($field->name))
                    ->numeric()
                    ->required($field->is_required)
                    ->rules([
                        'required_if:is_required,true',   
                        'numeric',                        
                        'min:0',                          
                        'max:1000000'                  
                    ]);
            case 'file':
                return FileUpload::make('uploadedFiles.' . $field->name)
                    ->label(ucfirst($field->name))
                    ->required($field->is_required)
                    ->directory('uploads')
                    ->rules(['file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:10240']) ; // max 10MB
            default:
                return TextInput::make($field->name)
                    ->label(ucfirst($field->name))
                    ->required($field->is_required)
                    ->rules(['required']);
        }
    }
    protected function getFormActions(): array
    {
        return [];
    }
 
}