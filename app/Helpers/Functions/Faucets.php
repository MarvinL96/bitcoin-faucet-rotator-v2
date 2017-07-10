<?php namespace App\Helpers\Functions;

use App\Http\Requests\CreateFaucetRequest;
use App\Http\Requests\UpdateFaucetRequest;
use App\Models\Faucet;
use App\Models\User;
use Carbon\Carbon;
use Laracasts\Flash\Flash as LaracastsFlash;
use App\Models\PaymentProcessor;
use App\Repositories\FaucetRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;

/**
 * Class Faucets
 *
 * A helper class to handle extra functionality
 * related to currently stored faucets.
 *
 * @author  Rob Attfield <emailme@robertattfield.com> <http://www.robertattfield.com>
 * @package App\Helpers\Functions
 */
class Faucets
{
    private $faucetRepository;

    /**
     * Faucets constructor.
     *
     * @param \App\Repositories\FaucetRepository $faucetRepository
     */
    public function __construct(FaucetRepository $faucetRepository)
    {
        $this->faucetRepository = $faucetRepository;
    }

    /**
     * Create and store a new faucet.
     *
     * @param CreateFaucetRequest $request
     */
    public function createStoreFaucet(CreateFaucetRequest $request)
    {
        $input = $request->except('payment_processors', 'slug', 'referral_code');

        $faucet = $this->faucetRepository->create($input);

        $paymentProcessors = $request->get('payment_processors');
        $referralCode = $request->get('referral_code');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $faucet->first()->paymentProcessors->detach();

        if (count($paymentProcessors) >= 1) {
            foreach ($paymentProcessors as $paymentProcessorId) {
                $faucet->first()->paymentProcessors->attach((int)$paymentProcessorId);
            }
        }

        if (Auth::user()->hasRole('owner')) {
            Auth::user()->faucets()->sync([$faucet->id => ['referral_code' => $referralCode]]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        activity()
            ->performedOn($faucet)
            ->causedBy(Auth::user())
            ->log("The faucet ':subject.name' was added to the collection by :causer.user_name");
    }

    /**
     * Update the specified faucet.
     *
     * @param  $slug
     * @param  UpdateFaucetRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateFaucet($slug, UpdateFaucetRequest $request)
    {
        $currentFaucet = $this->faucetRepository->findByField('slug', $slug, true)->first();

        $faucet = $this->faucetRepository->update($request->all(), $currentFaucet->id);

        $paymentProcessors = $request->get('payment_processors');
        $paymentProcessorIds = $request->get('payment_processors');

        $referralCode = $request->get('referral_code');

        if (count($paymentProcessorIds) == 1) {
            $paymentProcessors = PaymentProcessor::where('id', $paymentProcessorIds[0]);
        } elseif (count($paymentProcessorIds) >= 1) {
            $paymentProcessors = PaymentProcessor::whereIn('id', $paymentProcessorIds);
        }

        if (empty($faucet)) {
            LaracastsFlash::error('Faucet not found');

            return redirect(route('faucets.index'));
        }

        $toAddPaymentProcessorIds = [];

        foreach ($paymentProcessors->pluck('id')->toArray() as $key => $value) {
            array_push($toAddPaymentProcessorIds, (int)$value);
        }

        if (count($toAddPaymentProcessorIds) > 1) {
            $faucet->paymentProcessors()->sync($toAddPaymentProcessorIds);
        } elseif (count($toAddPaymentProcessorIds) == 1) {
            $faucet->paymentProcessors()->sync([$toAddPaymentProcessorIds[0]]);
        }

        if (Auth::user()->hasRole('owner')) {
            $faucet->users()->sync([Auth::user()->id => ['faucet_id' => $faucet->id, 'referral_code' => $referralCode]]);
        }

        activity()
            ->performedOn($faucet)
            ->causedBy(Auth::user())
            ->log("The faucet ':subject.name' was updated by :causer.user_name");
    }



    /**
     * Soft-delete or permanently delete a faucet.
     *
     * @param  $slug
     * @param  bool $permanentlyDelete
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroyFaucet($slug, $permanentlyDelete = false)
    {
        $faucet = $this->faucetRepository->findByField('slug', $slug)->first();
        $logFaucet = $this->faucetRepository->findByField('slug', $slug)->first();
        $logMessage = null;

        if (empty($faucet)) {
            LaracastsFlash::error('Faucet not found');

            return redirect(route('faucets.index'));
        }

        if (!empty($faucet) && $faucet->isDeleted()) {
            LaracastsFlash::error('The faucet has already been deleted.');

            return redirect(route('faucets.index'));
        }

        if ($permanentlyDelete == true) {
            $logMessage = "The faucet ':subject.name' was permanently deleted by :causer.user_name";
            $faucet->forceDelete();

        } else {
            $this->faucetRepository->deleteWhere(['slug' => $slug]);
            $logMessage = "The faucet ':subject.name' was archived/deleted by :causer.user_name";
        }

        activity()
            ->performedOn($logFaucet)
            ->causedBy(Auth::user())
            ->log($logMessage);
    }

    /**
     * Soft-delete or permanently delete a user's faucet and referral information.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Faucet $faucet
     * @param bool               $permanentlyDelete
     *
     * @return bool
     */
    public static function destroyUserFaucet(User $user, Faucet $faucet, bool $permanentlyDelete = false) 
    {
        if(empty($user) || empty($faucet)) {
            return false;
        }

        if($permanentlyDelete == false) {

            DB::table('referral_info')
                ->where('user_id', $user->id)
                ->where('faucet_id', $faucet->id)
                ->update(['deleted_at' => Carbon::now()]);

            activity()
                ->performedOn($faucet)
                ->causedBy(Auth::user())
                ->log("The faucet ':subject.name' in '" . $user->user_name . "'s' collection was archived/deleted by :causer.user_name");
        } else {

            DB::table('referral_info')
                ->where('user_id', $user->id)
                ->where('faucet_id', $faucet->id)
                ->delete();

            activity()
                ->performedOn($faucet)
                ->causedBy(Auth::user())
                ->log("The faucet ':subject.name' in '" . $user->user_name . "'s' collection was permanently deleted by :causer.user_name");
        }
    }

    /**
     * Restore a specified soft-deleted faucet.
     *
     * @param $slug
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function restoreFaucet($slug)
    {
        $faucet = $this->faucetRepository->findByField('slug', $slug)->first();
        $logFaucet = $this->faucetRepository->findByField('slug', $slug)->first();

        if (empty($faucet)) {
            LaracastsFlash::error('Faucet not found');

            return redirect(route('faucets.index'));
        }

        if (!empty($faucet) && !$faucet->isDeleted()) {
            LaracastsFlash::error('The faucet has already been restored or is still active.');

            return redirect(route('faucets.index'));
        }

        $this->faucetRepository->restoreDeleted($slug);

        activity()
            ->performedOn($logFaucet)
            ->causedBy(Auth::user())
            ->log("The faucet ':subject.name' was restored by :causer.user_name");
    }

    /**
     * Restore a user's faucet via pivot table.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Faucet $faucet
     *
     * @return bool
     */
    public static function restoreUserFaucet(User $user, Faucet $faucet) 
    {

        if(empty($user) || empty($faucet)) {
            return false;
        }

        DB::table('referral_info')
            ->where('user_id', $user->id)
            ->where('faucet_id', $faucet->id)
            ->update(['deleted_at' => null]);

        activity()
            ->performedOn($faucet)
            ->causedBy(Auth::user())
            ->log("The faucet ':subject.name' in '" . $user->user_name . "'s' collection was restored by :causer.user_name");

        return true;
    }


    /**
     * @param User   $user
     * @param Faucet $faucet
     * @return string
     */
    public static function getUserFaucetRefCode(User $user, Faucet $faucet)
    {

        // Check if the user and faucet exists.
        if (empty($user) || empty($faucet)) {
            return null;
        }

        $referralCode = DB::table('referral_info')->where(
            [
                ['faucet_id', '=', $faucet->id],
                ['user_id', '=', $user->id]
            ]
        )->first();

        return $referralCode != null ? $referralCode->referral_code : null;
    }

    /**
     * @param User   $user
     * @param Faucet $faucet
     * @param string $refCode
     * @return null
     */
    public static function setUserFaucetRefCode(User $user, Faucet $faucet, $refCode = null)
    {

        // Check if the user and faucet exists.
        if (empty($user) || empty($faucet)) {
            return null;
        }

        // Check if the user already has a matching ref code.
        $referralCode = self::getUserFaucetRefCode($user, $faucet);
        $refCode = Purifier::clean($refCode, 'generalFields');

        // If there is no matching ref code, add record to database.
        if (empty($referralCode) && !empty($refCode)) {
            DB::table('referral_info')->insert(
                [
                    'faucet_id' => $faucet->id,
                    'user_id' => $user->id,
                    'referral_code' => $refCode
                ]
            );
        } else {
            DB::table('referral_info')->where(
                [
                    ['faucet_id', '=', $faucet->id],
                    ['user_id', '=', $user->id]
                ]
            )->update(['referral_code' => $refCode]);
        }
    }

    /**
     * Retrieve the faucets of a specified user.
     *
     * @param  User $user
     * @param  bool $isDeleted
     * @return \Illuminate\Support\Collection
     */
    public function getUserFaucets(User $user, bool $isDeleted = false)
    {
        if (empty($user)) {
            return null;
        }

        $userFaucetIds = $this->getUserFaucetIds($user, $isDeleted);

        $faucets = $user->faucets()->whereIn('id', $userFaucetIds)->get();

        return $faucets;
    }

    /**
     * Retrieve faucet ids matching with specified user.
     *
     * @param  User $user
     * @param  bool $isDeleted
     * @return \Illuminate\Support\Collection
     */
    private function getUserFaucetIds(User $user, bool $isDeleted = false)
    {
        if ($isDeleted == true) {
            $userFaucetIds = DB::table('referral_info')->where(
                [
                    ['user_id', '=', $user->id],
                ]
            );
        } else {
            $userFaucetIds = DB::table('referral_info')->where(
                [
                    ['user_id', '=', $user->id],
                    ['deleted_at', '=', null]
                ]
            );
        }
        return $userFaucetIds->get()->pluck('faucet_id');
    }
}
