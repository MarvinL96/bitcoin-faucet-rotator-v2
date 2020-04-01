<?php

namespace App\Repositories;

use App\Models\Alert;
use Stevebauman\Purify\Facades\Purify;

/**
 * Class AlertRepository
 * @package App\Repositories
 * @version November 6, 2017, 6:39 pm NZDT
 * @author  Rob Attfield <emailme@robertattfield.com> <http://www.robertattfield.com>
 *
 * @method Alert findWithoutFail($id, $columns = ['*'])
 * @method Alert find($id, $columns = ['*'])
 * @method Alert first($columns = ['*'])
*/
class AlertRepository extends Repository implements IRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'slug',
        'summary',
        'content',
        'alert_type_id',
        'alert_icon_id',
        'hide_alert',
        'show_site_wide',
        'show_only_on_home_page',
        'sent_with_twitter',
        'publish_at',
        'hide_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Alert::class;
    }

    /**
     * Create a new alert.
     *
     * @param  array $data
     * @return Alert
     */
    public function create(array $data)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        !array_key_exists('hide_alert', $data) ? $data['hide_alert'] = 0: null;
        !array_key_exists('sent_with_twitter', $data) ? $data['sent_with_twitter'] = 0: null;

        $alertData = self::cleanInput($data);
        $alert = parent::create($alertData);
        $this->skipPresenter($temporarySkipPresenter);
        $this->updateRelations($alert, $alertData);
        $alert->save();
        return $this->parserResult($alert);
    }

    /**
     * Update the specified alert.
     *
     * @param array $data
     * @param $slug
     *
     * @return mixed
     */
    public function update(array $data, $slug)
    {
        //dd($data);
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $alertData = self::cleanInput($data);
        $alert = Alert::where('slug', $slug)->withTrashed()->first();
        $updatedAlert = $alert->fill($alertData);
        $this->skipPresenter($temporarySkipPresenter);
        $alert = $this->updateRelations($updatedAlert, $alertData);
        $updatedAlert->save();
        return $this->parserResult($updatedAlert);
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @param bool  $deleted
     *
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function findItemsWhere(array $where, $columns = ['*'], bool $deleted = false)
    {
        $this->applyCriteria();
        $this->applyScope();

        $this->applyConditions($where);

        $model = $deleted == true ? $this->model->withTrashed()->get($columns) : $this->model->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Method to sanitize data for classes implementing the IRepository interface.
     *
     * @param array $input
     *
     * @return mixed
     */
    public static function cleanInput(array $input)
    {
        $inputToSanitize = [
            'title' => Purify::clean($input['title'], self::$generalFieldsConfig),
            'summary' => Purify::clean($input['summary'], self::$generalFieldsConfig),
            'content' => Purify::clean($input['content']),
            'twitter_message' => Purify::clean($input['twitter_message'], self::$generalFieldsConfig),
            'keywords' => Purify::clean($input['keywords'], self::$generalFieldsConfig),
            'alert_type_id' => intval(Purify::clean($input['alert_type_id'], self::$generalFieldsConfig)),
            'alert_icon_id' => intval(Purify::clean($input['alert_icon_id'], self::$generalFieldsConfig)),
            'hide_alert' => intval(Purify::clean($input['hide_alert'], self::$generalFieldsConfig)),
            'sent_with_twitter' => intval(Purify::clean($input['sent_with_twitter'], self::$generalFieldsConfig))
        ];

        return $inputToSanitize;
    }
}
