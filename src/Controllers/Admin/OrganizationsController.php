<?php

declare(strict_types=1);

namespace Engelsystem\Controllers\Admin;

use Engelsystem\Controllers\BaseController;
use Engelsystem\Controllers\HasUserNotifications;
use Engelsystem\Http\Exceptions\ValidationException;
use Engelsystem\Http\Redirector;
use Engelsystem\Http\Request;
use Engelsystem\Http\Response;
use Engelsystem\Http\Validation\Validator;
use Engelsystem\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Psr\Log\LoggerInterface;

class OrganizationsController extends BaseController
{
    use HasUserNotifications;

    /** @var array<string> */
    protected array $permissions = [
        'admin_organizations',
    ];

    public function __construct(
        protected LoggerInterface $log,
        protected Organization $organization,
        protected Redirector $redirect,
        protected Response $response
    ) {
    }

    public function index(): Response
    {
        $organizations = $this->organization
            ->orderBy('name')
            ->get();

        return $this->response->withView(
            'admin/organizations/index',
            ['organizations' => $organizations, 'is_index' => true]
        );
    }

    public function show(Request $request): Response
    {
        $organizationId = (int) $request->getAttribute('organization_id');

        $organization = $this->organization->find($organizationId);

        return $this->showDetails($organization);
    }

    public function edit(Request $request): Response
    {
        $organizationId = (int) $request->getAttribute('organization_id');

        $organization = $this->organization->find($organizationId);

        return $this->showEdit($organization);
    }

    public function save(Request $request): Response
    {
        $organizationId = (int) $request->getAttribute('organization_id');

        /** @var Organization $organization */
        $organization = $this->organization->findOrNew($organizationId);

        if ($request->request->has('delete')) {
            return $this->delete($request);
        }

        $data = $this->validate(
            $request,
            [
                'name'              => 'required',
                'description'       => 'required|optional',
                'email'             => 'required|optional',
                'phone'             => 'required|optional',
                'contact_person'    => 'required|optional',
            ] 
        );

        if (Organization::whereName($data['name'])->where('id', '!=', $organization->id)->exists()) {
            throw new ValidationException((new Validator())->addErrors(['name' => ['validation.name.exists']]));
        }

        $organization->name = $data['name'];
        $organization->description = $data['description'];
        $organization->email = $data['email'];
        $organization->phone = $data['phone'];
        $organization->contact_person = $data['contact_person'];

        $organization->save();

        $this->log->info(
            'Updated organization "{name}": {description} {email} {phone} {contact_person}',
            [
                'name'              => $organization->name,
                'description'       => $organization->description,
                'email'             => $organization->email,
                'phone'             => $organization->phone,
                'contact_person'    => $organization->contact_person,
            ]
        );

        $this->addNotification('organization.edit.success');

        return $this->redirect->to('/admin/organizations');
    }

    public function delete(Request $request): Response
    {
        $data = $this->validate($request, [
            'id'     => 'required|int',
            'delete' => 'checked',
        ]);

        $organization = $this->organization->findOrFail($data['id']);

        $organization->delete();

        $this->log->info('Deleted organization {organization}', ['organization' => $organization->name]);
        $this->addNotification('organization.delete.success');

        return $this->redirect->to('/admin/organizations');
    }

    protected function showEdit(?Organization $organization): Response
    {
        return $this->response->withView(
            'admin/organizations/edit',
            ['organization' => $organization]
        );
    }
    protected function showDetails(?Organization $organization): Response
    {
        return $this->response->withView(
            'admin/organizations/show',
            ['organization' => $organization]
        );
    }
}
