<?php

namespace app\controllers;

use app\models\ShortUrl;
use app\models\VisitLog;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Random\RandomException;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UrlController extends Controller
{
    /**
     * @throws Exception
     * @throws RandomException
     */
    public function actionGenerate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new ShortUrl();

            if ($model->load(Yii::$app->request->post())) {
                $model->short_code = ShortUrl::generateShortCode();

                if ($model->validate()) {
                    if ($model->save()) {
                        $qrCode = new QrCode($model->getShortUrl());
                        $writer = new PngWriter();
                        $result = $writer->write($qrCode);

                        return [
                            'success' => true,
                            'short_url' => $model->getShortUrl(),
                            'qr_code' => base64_encode($result->getString()),
                        ];
                    } else {
                        Yii::error('Ошибка сохранения модели: ' . print_r($model->errors, true));
                        return [
                            'success' => false,
                            'error' => 'Ошибка при сохранении данных'
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'error' => $model->hasErrors() ? current($model->firstErrors) : 'Неизвестная ошибка'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Не удалось обработать данные формы'
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'Недопустимый метод запроса'
        ];
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionRedirect(string $id): Response
    {
        $model = ShortUrl::findOne(['short_code' => $id]);

        if ($model) {
            $model->incrementClickCount();

            VisitLog::logVisit($model->id, Yii::$app->request->userIP);

            return $this->redirect($model->original_url);
        }

        throw new NotFoundHttpException('Короткая ссылка не найдена');
    }
}