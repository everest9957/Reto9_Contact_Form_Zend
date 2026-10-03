<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\ContactForm;
use Application\Model\Contact;
use Application\Model\ContactTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ContactController extends AbstractActionController
{
    private ContactForm $form;
    private ContactTable $table;

    public function __construct(ContactForm $form, ContactTable $table)
    {
        $this->form  = $form;
        $this->table = $table;
    }

    /**
     * GET /contact
     * Muestra el formulario y los mensajes flash (éxito/error).
     */
    public function indexAction(): ViewModel
    {
        $flashMessenger = $this->flashMessenger();

        $successMessages = $flashMessenger->getSuccessMessages();
        $errorMessages   = $flashMessenger->getErrorMessages();

        return new ViewModel([
            'form'            => $this->form,
            'successMessages' => $successMessages,
            'errorMessages'   => $errorMessages,
        ]);
    }

    /**
     * POST /contact/process
     * Valida el formulario, guarda en BD y aplica patrón PRG
     * (Post/Redirect/Get) con mensajes flash.
     */
    public function processAction()
    {
        $request = $this->getRequest();

        // Solo se acepta POST
        if (! $request->isPost()) {
            return $this->redirect()->toRoute('contact');
        }

        // Cargar datos POST en el formulario
        $this->form->setData($request->getPost());

        // Validar
        if (! $this->form->isValid()) {
            // Guardar mensajes de error en flash y volver a /contact
            $messages = $this->form->getMessages();
            foreach ($messages as $field => $fieldMessages) {
                foreach ($fieldMessages as $message) {
                    $this->flashMessenger()->addErrorMessage(
                        sprintf('[%s] %s', $field, $message)
                    );
                }
            }

            return $this->redirect()->toRoute('contact');
        }

        // Datos válidos: guardar en BD
        $data = $this->form->getData();

        $contact = new Contact();
        $contact->nombre  = $data['nombre'];
        $contact->email   = $data['email'];
        $contact->mensaje = $data['mensaje'];

        $this->table->saveContact($contact);

        $this->flashMessenger()->addSuccessMessage(
            '¡Gracias! Tu mensaje ha sido enviado correctamente.'
        );

        // PRG: redirigir a la vista de contacto
        return $this->redirect()->toRoute('contact');
    }
}