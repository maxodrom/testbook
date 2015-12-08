<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Book;

/**
 * BookSearch represents the model behind the search form about `common\models\Book`.
 */
class BookSearch extends Book
{
    public $from_date;
    public $to_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date_create', 'date_update', 'author_id'], 'integer'],
            [['name', 'preview', 'date', 'from_date', 'to_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Book::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
            'date' => $this->date,
            'author_id' => $this->author_id,
        ]);

        if (isset($params['BookSearch']['from_date']) && preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $params['BookSearch']['from_date'], $matches)) {
            $query->andWhere([
                '>=',
                'date',
                $matches[3].'-'.$matches[2].'-'.$matches[1]
            ]);
        }

        if (isset($params['BookSearch']['to_date']) && preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $params['BookSearch']['to_date'], $matches)) {
            $query->andWhere([
                '<=',
                'date',
                $matches[3].'-'.$matches[2].'-'.$matches[1]
            ]);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'preview', $this->preview]);

        return $dataProvider;
    }
}
