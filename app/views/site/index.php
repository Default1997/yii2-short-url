<?php

use app\models\ShortUrl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var ShortUrl $model */

$this->title = 'Сокращателинатор URL';
$generateUrl = Url::to(['url/generate']);
?>

    <div class="container-sm py-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body p-4">
                <h1 class="h4 card-title text-center mb-4"><?= Html::encode($this->title) ?></h1>

                <?php $form = ActiveForm::begin(['id' => 'url-form']); ?>

                <div class="input-group mb-3">
                    <?= $form->field($model, 'original_url', [
                        'options' => ['class' => 'flex-grow-1 mb-0'],
                        'inputOptions' => ['class' => 'form-control rounded-end-0']
                    ])
                        ->textInput([
                            'placeholder' => 'Вставьте ссылку',
                            'required' => true
                        ])
                        ->label(false) ?>

                    <div class="input-group-append">
                        <?= Html::submitButton('ОК', [
                            'class' => 'btn btn-primary rounded-start-0',
                            'id' => 'submit-btn'
                        ]) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

                <div id="loading" class="d-none text-center py-2">
                    <div class="spinner-border" role="status"></div>
                </div>

                <div id="result" class="d-none mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="short-url" readonly style="min-width: 0;">
                        <button class="btn btn-outline-primary" id="copy-btn" type="button">Копировать</button>
                    </div>
                    <div class="text-center mt-3">
                        <img id="qr-code" class="rounded bg-white border p-2" style="max-width: 180px;">
                    </div>
                </div>

                <div id="error-message" class="alert alert-danger mt-3 d-none"></div>
            </div>
        </div>
    </div>

<?php
$this->registerJs(<<<JS
    const \$form = $('#url-form');
    const \$btn = $('#submit-btn');
    const \$result = $('#result');
    const \$error = $('#error-message');
    const \$loading = $('#loading');

    \$form.on('beforeSubmit', function() {
        \$btn.prop('disabled', true).text('...');
        \$loading.removeClass('d-none');
        \$result.addClass('d-none');
        \$error.addClass('d-none');

        \$.post('$generateUrl', \$form.serialize()).done(data => {
            if (data.success) {
                $('#short-url').val(data.short_url).select();
                $('#qr-code').attr('src', 'data:image/png;base64,' + data.qr_code);
                \$result.removeClass('d-none');
            } else {
                \$error.text(data.error).removeClass('d-none');
            }
        }).fail(() => {
            \$error.text('Ошибка сети').removeClass('d-none');
        }).always(() => {
            \$btn.prop('disabled', false).text('ОК');
            \$loading.addClass('d-none');
        });
        
        return false;
    });

    $('#copy-btn').on('click', () => {
        const el = $('#short-url')[0];
        el.select();
        document.execCommand('copy');
        
        const original = \$btn.text();
        \$btn.text('✓');
        setTimeout(() => \$btn.text(original), 1500);
    });
JS);
?>