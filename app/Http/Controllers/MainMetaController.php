<?php

namespace App\Http\Controllers;

use App\Exports\MainMetaCsvExport;
use App\Helpers\Functions;
use App\Helpers\Functions\Users;
use App\Http\Requests\CreateMainMetaRequest;
use App\Http\Requests\UpdateMainMetaRequest;
use App\Models\Language;
use App\Repositories\MainMetaRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class MainMetaController
 *
 * @author  Rob Attfield <emailme@robertattfield.com> <http://www.robertattfield.com>
 * @package App\Http\Controllers
 */
class MainMetaController extends AppBaseController
{
    private $mainMetaRepository;
    private $languageCodes;

    /**
     * MainMetaController constructor.
     *
     * @param MainMetaRepository $mainMetaRepo
     */
    public function __construct(MainMetaRepository $mainMetaRepo)
    {
        $this->mainMetaRepository = $mainMetaRepo;
        $this->languageCodes = Language::orderBy('name')->pluck('name', 'iso_code');

        $this->middleware('auth');
    }

    /**
     * Store a newly created MainMeta in storage.
     *
     * @param CreateMainMetaRequest $request
     * @return RedirectResponse|Redirector
     * @throws ValidatorException
     */
    public function store(CreateMainMetaRequest $request)
    {
        Functions::userCanAccessArea(Auth::user(), 'main-meta.store', [], []);
        $input = $request->all();

        $this->mainMetaRepository->create($input);

        flash('Main Meta updated successfully.')->success();

        return redirect(route('settings') . "#main-meta");
    }

    /**
     * Update the specified MainMeta in storage.
     *
     * @param int $id
     * @param UpdateMainMetaRequest $request
     * @return RedirectResponse|Redirector
     * @throws ValidatorException
     */
    public function update($id, UpdateMainMetaRequest $request)
    {
        Functions::userCanAccessArea(Auth::user(), 'main-meta.update', ['id' => $id], ['id' => $id]);
        $mainMeta = $this->mainMetaRepository->find($id);

        if (empty($mainMeta)) {
            flash('Main Meta not found.')->error();

            return redirect(route('settings') . "#main-meta");
        }

        $input = $request->all();

        $this->mainMetaRepository->update($input, $id);

        flash('Main Meta updated successfully.')->success();

        return redirect(route('settings') . "#main-meta");
    }

    /**
     * Remove the specified MainMeta from storage.
     *
     * @param  int $id
     * @return RedirectResponse|Redirector
     */
    public function destroy($id)
    {
        Functions::userCanAccessArea(Auth::user(), 'main-meta.destroy', ['id' => $id], ['id' => $id]);
        $mainMeta = $this->mainMetaRepository->findWithoutFail($id);

        if (empty($mainMeta)) {
            flash('Main Meta not found.')->success();

            return redirect(route('main-meta.index'));
        }

        $this->mainMetaRepository->delete($id);

        flash('Main meta deleted successfully.')->success();

        return redirect(route('main-meta.index'));
    }

    public function exportCSV()
    {
        Users::userCanAccessArea(Auth::user(), 'main-meta.export-as-csv', [], []);
        return Excel::download(new MainMetaCsvExport(), 'main_meta.csv');
    }
}
