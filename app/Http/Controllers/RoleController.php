<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\RoleRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Response;

/**
 * Class RoleController
 *
 * @author  Rob Attfield <emailme@robertattfield.com> <http://www.robertattfield.com>
 * @package App\Http\Controllers
 */
class RoleController extends AppBaseController
{
    private $roleRepository;

    /**
     * RoleController constructor.
     *
     * @param RoleRepository $roleRepo
     */
    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
        $this->middleware('auth');
    }

    /**
     * Display the specified Role.
     *
     * @param string $slug
     *
     * @return Factory|View
     * @throws RepositoryException
     */
    public function show($slug)
    {
        Functions::userCanAccessArea(Auth::user(), 'roles.show', ['slug' => $slug], ['slug' => $slug]);
        $role = $this->roleRepository->findByField('slug', $slug)->first();

        if (empty($role)) {
            flash('Role not found.')->error();

            return redirect(route('roles.index'));
        }

        return view('roles.show')->with('role', $role);
    }

    /**
     * Show the form for editing the specified Role.
     *
     * @param string $slug
     *
     * @return Factory|View
     * @throws RepositoryException
     */
    public function edit($slug)
    {
        Functions::userCanAccessArea(Auth::user(), 'roles.edit', ['slug' => $slug], ['slug' => $slug]);
        $role = $this->roleRepository->findByField('slug', $slug)->first();

        if (empty($role)) {
            flash('Role not found.')->error();

            return redirect(route('roles.index'));
        }

        return view('roles.edit')->with('role', $role);
    }

    /**
     * Update the specified Role in storage.
     *
     * @param string $slug
     * @param UpdateRoleRequest $request
     *
     * @return Response
     * @throws RepositoryException
     * @throws ValidatorException
     */
    public function update($slug, UpdateRoleRequest $request)
    {
        Functions::userCanAccessArea(Auth::user(), 'roles.update', ['slug' => $slug], ['slug' => $slug]);
        $role = $this->roleRepository->findByField('slug', $slug)->first();

        if (empty($role)) {
            flash('Role not found.')->error();

            return redirect(route('roles.index'));
        }

        $this->roleRepository->update($request->all(), $role->id);

        flash('Role updated successfully.')->success();

        return redirect(route('roles.index'));
    }
}
