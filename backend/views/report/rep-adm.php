<?php

use common\models\FederalDistrict;
use common\models\Municipality;
use common\models\Organization;
use common\models\Region;
use common\models\User;
use common\models\UserEdu20;
use common\models\UserEdu21;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$federal_district_null = array('0' => 'Все федеральные округа');
$federal_district = FederalDistrict::find()->all();
$federal_district_item = ArrayHelper::map($federal_district, 'id', 'name');
$federal_district_item = ArrayHelper::merge($federal_district_null, $federal_district_item);

if (!empty($post['federal_district_id'])) {
    $region_null = array('0' => 'Все субъекты федерации');
    $region = Region::find()->where(['district_id' => $post['federal_district_id']])->all();
    $region_item = ArrayHelper::map($region, 'id', 'name');
    $region_item = ArrayHelper::merge($region_null, $region_item);
} else {
    $region_item = array('0' => 'Все субъекты федерации');
}

if (!empty($post['region_id'])) {
    $municipality_null = array('0' => 'Все муниципальные образования');
    $municipality = Municipality::find()->where(['region_id' => $post['region_id']])->all();
    $municipality_item = ArrayHelper::map($municipality, 'id', 'name');
    $municipality_item = ArrayHelper::merge($municipality_null, $municipality_item);
} else {
    $municipality_item = array('0' => 'Все муниципальные образования');
}

if (!empty($post['municipality_id'])) {
    $name_organization_null = array('0' => 'Все организации');
    $name_organizations = Organization::find()->where(['municipality_id' => $post['municipality_id']])->all();
    $name_organization_items = ArrayHelper::map($name_organizations, 'id', 'short_title');
    $name_organization_items = ArrayHelper::merge($name_organization_null, $name_organization_items);
} else {
    $name_organization_items = array('0' => 'Все организации');
}

$two_column = [
    'options' => ['class' => 'row mt-4'],
    'labelOptions' => ['class' => 'col-2 col-form-label font-weight-bold']
];
$year_ar = [2022 => 2022, 2021 => 2021, 2020 => 2020];

$user_model = new User();
$user_model21 = new UserEdu21();
$user_model20 = new UserEdu20();
?>
    <div class="report-rpn container">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="text-center"><h4>Отчёт по школьной программе</h4></div>
                <?
                $form = ActiveForm::begin(); ?>

                <div class="row">
                    <div class="col font-weight-bold">
                        Год обучения
                    </div>
                    <div class="col">
                        <?= $form->field($model, 'title')->dropDownList($year_ar,
                            ['options' => [$post['title'] => ['Selected' => true]]])->label(false); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col font-weight-bold">
                        Федеральный округ
                    </div>
                    <div class="col">
                        <?= $form->field($model, 'federal_district_id')->dropDownList($federal_district_item,
                            [
                                'options' => [$post['federal_district_id'] => ['Selected' => true]],
                                'onchange' => '
                                     var id_f = $(this).val();
                                     $.get("../report/subjectslist?id_f="+id_f, function(data){
                                        $("select#organization-region_id").html(data);
                                     });'
                            ])->label(false); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col font-weight-bold">
                        Субъект федерации
                    </div>
                    <div class="col">
                        <?= $form->field($model, 'region_id')->dropDownList($region_item, [
                            'options' => [$post['region_id'] => ['Selected' => true]],
                            'onchange' => '
                                 var id_r = $(this).val();
                                 $.get("../report/municipalitylist?id_r="+id_r, function(data){
                                    $("select#organization-municipality_id").html(data);
                                 });'
                        ])->label(false); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col font-weight-bold">
                        Муниципальное образование
                    </div>
                    <div class="col">
                        <?= $form->field($model, 'municipality_id')->dropDownList($municipality_item,
                            [
                                'options' => [$post['municipality_id'] => ['Selected' => true]],
                                'onchange' => '
                                   var id_m = $(this).val();
                                   $.get("../report/organization-name-school?id_m="+id_m, function(data){
                                      $("select#organization-short_title").html(data);
                                   });'
                            ])->label(false); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col font-weight-bold">
                        Наименование организации
                    </div>
                    <div class="col">
                        <?= $form->field($model, 'short_title')->dropDownList($name_organization_items,
                            ['options' => [$post['short_title'] => ['Selected' => true]]])->label(false); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group" style="margin: 0 auto">
                        <?= Html::submitButton('Посмотреть', [
                            'name' => 'identificator',
                            'value' => 'view',
                            'class' => 'mt-2 btn main-button-3 beforeload'
                        ]) ?>
                        <button class="btn main-button-3 mt-2 load" type="button" disabled style="display: none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Пожалуйста, подождите...
                        </button>
                    </div>
                </div>
                <?php
                ActiveForm::end(); ?>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
<?php
if (!empty($org)) {
    ?>
    <table id="tableId" class="table table-hover table-bordered table-striped mt-3 table2excel_with_colors">
        <thead>
        <tr class="text-center">
            <th rowspan="2">№</th>
            <th rowspan="2" style="max-width: 120px;">Уникальный номер</th>
            <th rowspan="2" style="max-width: 120px;">Тип слушателя</th>
            <th rowspan="2" style="max-width: 120px;">Класс</th>
            <th rowspan="2" style="max-width: 120px;">Входной балл</th>
            <th colspan="6" style="max-width: 120px;">Школьная программа</th>

            <th rowspan="2" style="max-width: 200px;">Итоговый балл (%)</th>
            <th rowspan="2" style="max-width: 200px;">Итоговый тест пройден с 1-ого раза</th>
            <th rowspan="2" style="max-width: 200px;">Итоговый тест пройден со 2-ого раза или более</th>
            <th rowspan="2" style="max-width: 200px;">Обучение завершено</th>
        </tr>
        <tr class="text-center">
            <?
            foreach ($themes as $them) {
                ?>
                <th style="max-width: 120px;"><?= $them->short_name ?></th>
                <?
            }
            ?>
            <th style="max-width: 150px;">Самостоятельная работа<br></th>
        </tr>
        </thead>
        <tbody>
        <?
        $count = 1;
        $input_test_total = 0;
        $theme1_total = 0;
        $theme2_total = 0;
        $theme3_total = 0;
        $theme4_total = 0;
        $theme5_total = 0;
        $independent_work_total = 0;
        $final_test_total = 0;
        $final_test_count_total = 0;
        $final_test_1st_total = 0;
        $final_test_2st_total = 0;
        $training_completed_total = 0;
        foreach ($data as $d) {
            ?>
            <tr class="text-center <?
            if ($count == 1) {
                echo 'prepend-org';
            } ?>">
                <td><?= $count++ ?></td>
                <td><?= $d->key_login ?></td>
                <td><?= $d->type_listener ?></td>
                <td><?= $d->class_number . ' ' . $d->letter_number ?></td>
                <td><?= $d->input_test . '0%';
                    $input_test_total += $d->input_test ?></td>

                <td><?= (!empty($d->theme1)) ? $d->theme1 : 0;
                    $theme1_total += $d->theme1; ?></td>
                <td><?= (!empty($d->theme2)) ? $d->theme2 : 0;
                    $theme2_total += $d->theme2; ?></td>
                <td><?= (!empty($d->theme3)) ? $d->theme3 : 0;
                    $theme3_total += $d->theme3; ?></td>
                <td><?= (!empty($d->theme4)) ? $d->theme4 : 0;
                    $theme4_total += $d->theme4; ?></td>
                <td><?= (!empty($d->theme5)) ? $d->theme5 : 0;
                    $theme5_total += $d->theme5; ?></td>
                <td><?= (!empty($d->independent_work)) ? $d->independent_work : 0;
                    $independent_work_total += $d->independent_work; ?></td>

                <td><?
                    if (isset($d->final_test)) {
                        echo $d->final_test . '0%';
                        $final_test_count_total++;
                        $final_test_total += $d->final_test;
                    } else {
                        echo '-';
                    } ?></td>
                <td><?= (!empty($d->final_test_1st)) ? $d->final_test_1st : 0;
                    $final_test_1st_total += $d->final_test_1st; ?></td>
                <td><?= (!empty($d->final_test_2st)) ? $d->final_test_2st : 0;
                    $final_test_2st_total += $d->final_test_2st; ?></td>
                <td><?= (!empty($d->training_completed)) ? $d->training_completed : 0;
                    $training_completed_total += $d->training_completed; ?></td>
            </tr>
            <?
        }
        ?>
        <tr class="text-center font-weight-bold bg-warning">
            <td colspan="4">Итого</td>
            <td><?= ($count < 2) ? '0' : round($input_test_total * 10 / ($count - 1), 1) . '%' ?></td>
            <td><?= $theme1_total ?></td>
            <td><?= $theme2_total ?></td>
            <td><?= $theme3_total ?></td>
            <td><?= $theme4_total ?></td>
            <td><?= $theme5_total ?></td>
            <td><?= $independent_work_total ?></td>
            <td><?= ($final_test_count_total == 0) ? '0' : round($final_test_total * 10 / $final_test_count_total,
                        1) . '%' ?></td>
            <td><?= $final_test_1st_total ?></td>
            <td><?= $final_test_2st_total ?></td>
            <td><?= $training_completed_total ?></td>
        </tr>
        </tbody>
    </table>
    <input type="button" class="btn btn-warning btn-block table2excel mb-3 mt-3"
           title="Вы можете скачать в формате Excel" value="Скачать в Excel" id="pechat222">
    <?php
} //по организации

elseif (!empty($mun_org)) //по муниципальному
{
    if ($post['title'] == 2022) {
        $mun_array = $user_model->reportNew($organizations[0]['municipality_id'], 1, 2);
    } elseif ($post['title'] == 2021) {
        $mun_array = $user_model21->reportNew($organizations[0]['municipality_id'], 1, 2);
    } elseif ($post['title'] == 2020) {
        $mun_array = $user_model20->reportNew($organizations[0]['municipality_id'], 1, 2);
    }
    if (!isset($mun_array[0]) || !isset($mun_array[1])) {
        echo '<br><p class="text-center text-danger font-weight-bold">К обучению не приступила ни одна организация!</p>';
    } else {
        ?>
        <table id="tableId" class="table table-hover table-bordered table-striped mt-3 table2excel_with_colors">
            <thead>
            <tr class="text-center">
                <th rowspan="2">№</th>
                <th rowspan="2">Название организации</th>
                <th rowspan="2" style="max-width: 120px;">Приступили к обучению (1-да, 0-нет)</th>

                <th colspan="3" style="max-width: 180px;">Количество зарегистрировавшихся человек</th>
                <th colspan="10">Количество человек прошедших обучение</th>
                <th colspan="3">Завершили обучение</th>
            </tr>
            <tr class="text-center">
                <th style="max-width: 122px;">Всего</th>
                <th style="max-width: 122px;">Взрослых</th>
                <th style="max-width: 122px;">Детей</th>

                <th style="max-width: 150px;">Входной тест (среднее значение %)</th>
                <?
                foreach ($themes as $them) {
                    ?>
                    <th style="max-width: 120px;"><?= $them->short_name ?></th>
                    <?
                } ?>
                <th style="max-width: 140px;">Самостоятельная работа</th>
                <th style="max-width: 122px;">Итоговый тест (среднее значение %)</th>
                <th style="max-width: 150px;">Итоговый тест пройден с 1-ого раза</th>
                <th style="max-width: 150px;">Итоговый тест пройден со 2-ого раза или более</th>

                <th style="max-width: 122px;">Всего</th>
                <th style="max-width: 122px;">Взрослых</th>
                <th style="max-width: 122px;">Детей</th>
            </tr>
            </thead>
            <tbody>
            <?
            $count = 1;
            $start_work_total = 0;
            $people_total = 0;
            $parent_total = 0;
            $child_total = 0;
            $inputTest_total = 0;
            $inputTest_count = 0;
            $theme1_total = 0;
            $theme2_total = 0;
            $theme3_total = 0;
            $theme4_total = 0;
            $theme5_total = 0;
            $independentWork_total = 0;
            $finalTest_total = 0;
            $finalTest_count = 0;
            $finalTest_1st_total = 0;
            $finalTest_2st_total = 0;
            $trainingCompleted_total = 0;

            $trainingCompletedAll_total = 0;
            $trainingCompletedParent_total = 0;
            $trainingCompletedChild_total = 0;


            foreach ($organizations as $organization) {
                /**/
                if (array_key_exists($organization->id . '_training_id_1',
                        $mun_array[0]) || array_key_exists($organization->id . '_training_id_2', $mun_array[0])) {
                    $people = $mun_array[0][$organization->id . '_training_id_1'] + $mun_array[0][$organization->id . '_training_id_2'];
                    $people_total += $people;
                } else {
                    $people = 0;
                }
                if (array_key_exists($organization->id . '_training_id_2', $mun_array[0])) {
                    $parent = $mun_array[0][$organization->id . '_training_id_2'];
                    $parent_total += $parent;
                } else {
                    $parent = 0;
                }
                if (array_key_exists($organization->id . '_training_id_1', $mun_array[0])) {
                    $child = $mun_array[0][$organization->id . '_training_id_1'];
                    $child_total += $child;
                } else {
                    $child = 0;
                }
                if (array_key_exists($organization->id . '_inputTestCount_calc', $mun_array[1])) {
                    $inputTest = $mun_array[1][$organization->id . '_inputTest_calc'] * 10 / $mun_array[1][$organization->id . '_inputTestCount_calc'];
                    $inputTest_total += $inputTest;
                    $inputTest_count++;

                    $start_work = 1;
                    $start_work_total++;
                } //входной тест
                else {
                    $inputTest = 0;
                    $start_work = 0;
                }

                if (array_key_exists($organization->id . '_theme1', $mun_array[1])) {
                    $theme1 = $mun_array[1][$organization->id . '_theme1'];
                    $theme1_total += $theme1;
                } else {
                    $theme1 = 0;
                }
                if (array_key_exists($organization->id . '_theme2', $mun_array[1])) {
                    $theme2 = $mun_array[1][$organization->id . '_theme2'];
                    $theme2_total += $theme2;
                } else {
                    $theme2 = 0;
                }
                if (array_key_exists($organization->id . '_theme3', $mun_array[1])) {
                    $theme3 = $mun_array[1][$organization->id . '_theme3'];
                    $theme3_total += $theme3;
                } else {
                    $theme3 = 0;
                }
                if (array_key_exists($organization->id . '_theme4', $mun_array[1])) {
                    $theme4 = $mun_array[1][$organization->id . '_theme4'];
                    $theme4_total += $theme4;
                } else {
                    $theme4 = 0;
                }
                if (array_key_exists($organization->id . '_theme5', $mun_array[1])) {
                    $theme5 = $mun_array[1][$organization->id . '_theme5'];
                    $theme5_total += $theme5;
                } else {
                    $theme5 = 0;
                }
                if (array_key_exists($organization->id . '_independentWork', $mun_array[1])) {
                    $independentWork = $mun_array[1][$organization->id . '_independentWork'];
                    $independentWork_total += $independentWork;
                } else {
                    $independentWork = 0;
                }
                if (array_key_exists($organization->id . '_finalTestCount_calc', $mun_array[1])) {
                    $finalTest = $mun_array[1][$organization->id . '_finalTest_calc'] * 10 / $mun_array[1][$organization->id . '_finalTestCount_calc'];
                    $finalTest_total += $finalTest;
                    $finalTest_count++;
                } //итоговый тест
                else {
                    $finalTest = 0;
                }
                if (array_key_exists($organization->id . '_finalTest_1st', $mun_array[1])) {
                    $finalTest_1st = $mun_array[1][$organization->id . '_finalTest_1st'];
                    $finalTest_1st_total += $finalTest_1st;
                } else {
                    $finalTest_1st = 0;
                }
                if (array_key_exists($organization->id . '_finalTest_2st', $mun_array[1])) {
                    $finalTest_2st = $mun_array[1][$organization->id . '_finalTest_2st'];
                    $finalTest_2st_total += $finalTest_2st;
                } else {
                    $finalTest_2st = 0;
                }
                if (array_key_exists($organization->id . '_trainingCompletedAll', $mun_array[1])) {
                    $trainingCompletedAll = $mun_array[1][$organization->id . '_trainingCompletedAll'];
                    $trainingCompletedAll_total += $trainingCompletedAll;
                } else {
                    $trainingCompletedAll = 0;
                }
                if (array_key_exists($organization->id . '_trainingCompletedParent', $mun_array[1])) {
                    $trainingCompletedParent = $mun_array[1][$organization->id . '_trainingCompletedParent'];
                    $trainingCompletedParent_total += $trainingCompletedParent;
                } else {
                    $trainingCompletedParent = 0;
                }
                if (array_key_exists($organization->id . '_trainingCompletedChild', $mun_array[1])) {
                    $trainingCompletedChild = $mun_array[1][$organization->id . '_trainingCompletedChild'];
                    $trainingCompletedChild_total += $trainingCompletedChild;
                } else {
                    $trainingCompletedChild = 0;
                }
                /*(END)*/
                ?>

                <tr class="text-center <?
                if ($count == 1) {
                    echo 'prepend-mun';
                } ?>">
                    <td><?= $count++ ?></td>
                    <td><?= $organization->short_title ?></td>
                    <td><?= $start_work ?></td>
                    <td><?= $people ?></td>
                    <td><?= $parent ?></td>
                    <td><?= $child ?></td>
                    <td><?= round($inputTest, 1) ?></td>
                    <td><?= $theme1 ?></td>
                    <td><?= $theme2 ?></td>
                    <td><?= $theme3 ?></td>
                    <td><?= $theme4 ?></td>
                    <td><?= $theme5 ?></td>
                    <td><?= $independentWork ?></td>
                    <td><?= round($finalTest, 1) ?></td>
                    <td><?= $finalTest_1st ?></td>
                    <td><?= $finalTest_2st ?></td>
                    <td><?= $trainingCompletedAll ?></td>
                    <td><?= $trainingCompletedParent ?></td>
                    <td><?= $trainingCompletedChild ?></td>
                </tr>
                <?
            }
            ?>
            <tr class="text-center font-weight-bold bg-warning">
                <td colspan="2">Итого</td>
                <td><?= $start_work_total ?></td>
                <td><?= $people_total ?></td>
                <td><?= $parent_total ?></td>
                <td><?= $child_total ?></td>
                <td><?= ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?></td>
                <td><?= $theme1_total ?></td>
                <td><?= $theme2_total ?></td>
                <td><?= $theme3_total ?></td>
                <td><?= $theme4_total ?></td>
                <td><?= $theme5_total ?></td>
                <td><?= $independentWork_total ?></td>
                <td><?= ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?></td>
                <td><?= $finalTest_1st_total ?></td>
                <td><?= $finalTest_2st_total ?></td>
                <td><?= $trainingCompletedAll_total ?></td>
                <td><?= $trainingCompletedParent_total ?></td>
                <td><?= $trainingCompletedChild_total ?></td>
            </tr>
            </tbody>
        </table>
        <input type="button" class="btn btn-warning btn-block table2excel mb-3 mt-3"
               title="Вы можете скачать в формате Excel" value="Скачать в Excel" id="pechat222">
        <?
    }
} //по муниципальному

elseif (!empty($reg_org)) //по региону
{
    if ($post['title'] == 2022) {
        $reg_array = $user_model->reportNew($municipalitys[0]['region_id'], 1, 3);
    } elseif ($post['title'] == 2021) {
        $reg_array = $user_model21->reportNew($municipalitys[0]['region_id'], 1, 3);
    } elseif ($post['title'] == 2020) {
        $reg_array = $user_model20->reportNew($municipalitys[0]['region_id'], 1, 3);
    }
    ?>
    <table id="tableId" class="table table-hover table-bordered table-striped mt-3 table2excel_with_colors">
        <thead>
        <tr class="text-center">
            <th rowspan="2">№</th>
            <th rowspan="2">Муниципальный район</th>
            <th rowspan="2" style="max-width: 120px;">Приступили к обучению (1-да, 0-нет)</th>
            <th rowspan="2" style="max-width: 160px;">Количество образовательных организаций приступивших к работе
            </th>

            <th colspan="3" style="max-width: 180px;">Количество зарегистрировавшихся человек</th>
            <th colspan="10">Количество человек прошедших обучение</th>
            <th colspan="3">Завершили обучение</th>
        </tr class="text-center">
        <tr class="text-center">
            <th style="max-width: 122px;">Всего</th>
            <th style="max-width: 122px;">Взрослых</th>
            <th style="max-width: 122px;">Детей</th>

            <th style="max-width: 150px;">Входной тест (среднее значение %)</th>
            <?
            foreach ($themes as $them) {
                ?>
                <th style="max-width: 120px;"><?= $them->short_name ?></th>
                <?
            } ?>
            <th style="max-width: 135px;">Самостоятельная работа</th>
            <th style="max-width: 122px;">Итоговый тест (среднее значение %)</th>
            <th style="max-width: 150px;">Итоговый тест пройден с 1-ого раза</th>
            <th style="max-width: 150px;">Итоговый тест пройден со 2-ого раза или более</th>

            <th style="max-width: 122px;">Всего</th>
            <th style="max-width: 122px;">Взрослых</th>
            <th style="max-width: 122px;">Детей</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        $startWork_total = 0;
        $countOrgStart_total = 0;
        $people_total = 0;
        $parent_total = 0;
        $child_total = 0;
        $inputTest_total = 0;
        $inputTest_count = 0;
        $theme1_total = 0;
        $theme2_total = 0;
        $theme3_total = 0;
        $theme4_total = 0;
        $theme5_total = 0;
        $independentWork_total = 0;
        $finalTest_total = 0;
        $finalTest_count = 0;
        $finalTest_1st_total = 0;
        $finalTest_2st_total = 0;
        $trainingCompleted_total = 0;

        $trainingCompletedAll_total = 0;
        $trainingCompletedParent_total = 0;
        $trainingCompletedChild_total = 0;

        foreach ($municipalitys as $municipality) {
            /**/
            if (array_key_exists($municipality->id . '_countOrgStart',
                $reg_array[1])) //"Приступили к обучению" && "Количество образовательных организаций приступивших к работе"
            {
                $countOrgStart = $reg_array[1][$municipality->id . '_countOrgStart'];
                $countOrgStart_total += $countOrgStart;
                $startWork = 1;
                $startWork_total++;
            } else {
                $countOrgStart = 0;
                $startWork = 0;
            }

            if (array_key_exists($municipality->id . '_training_id_1',
                    $reg_array[0]) || array_key_exists($municipality->id . '_training_id_2',
                    $reg_array[0])) //Количество зарегистрировавшихся человек (всего)
            {
                $people = $reg_array[0][$municipality->id . '_training_id_1'] + $reg_array[0][$municipality->id . '_training_id_2'];
                $people_total += $people;
            } else {
                $people = 0;
            }

            if (array_key_exists($municipality->id . '_training_id_2',
                $reg_array[0])) //Количество зарегистрировавшихся человек (взрослых)
            {
                $parent = $reg_array[0][$municipality->id . '_training_id_2'];
                $parent_total += $parent;
            } else {
                $parent = 0;
            }
            if (array_key_exists($municipality->id . '_training_id_1',
                $reg_array[0])) //Количество зарегистрировавшихся человек (детей)
            {
                $child = $reg_array[0][$municipality->id . '_training_id_1'];
                $child_total += $child;
            } else {
                $child = 0;
            }

            if (array_key_exists($municipality->id . '_inputTest', $reg_array[1])) //входной тест
            {
                $inputTest = $reg_array[1][$municipality->id . '_inputTest'] / $countOrgStart;
                $inputTest_total += $inputTest;
                $inputTest_count++;
            } //входной тест
            else {
                $inputTest = 0;
            }
            if (array_key_exists($municipality->id . '_theme1', $reg_array[1])) {
                $theme1 = $reg_array[1][$municipality->id . '_theme1'];
                $theme1_total += $theme1;
            } else {
                $theme1 = 0;
            }
            if (array_key_exists($municipality->id . '_theme2', $reg_array[1])) {
                $theme2 = $reg_array[1][$municipality->id . '_theme2'];
                $theme2_total += $theme2;
            } else {
                $theme2 = 0;
            }
            if (array_key_exists($municipality->id . '_theme3', $reg_array[1])) {
                $theme3 = $reg_array[1][$municipality->id . '_theme3'];
                $theme3_total += $theme3;
            } else {
                $theme3 = 0;
            }
            if (array_key_exists($municipality->id . '_theme4', $reg_array[1])) {
                $theme4 = $reg_array[1][$municipality->id . '_theme4'];
                $theme4_total += $theme4;
            } else {
                $theme4 = 0;
            }
            if (array_key_exists($municipality->id . '_theme5', $reg_array[1])) {
                $theme5 = $reg_array[1][$municipality->id . '_theme5'];
                $theme5_total += $theme5;
            } else {
                $theme5 = 0;
            }
            if (array_key_exists($municipality->id . '_independentWork', $reg_array[1])) {
                $independentWork = $reg_array[1][$municipality->id . '_independentWork'];
                $independentWork_total += $independentWork;
            } else {
                $independentWork = 0;
            }

            if (array_key_exists($municipality->id . '_finalTest', $reg_array[1])) //итоговый тест
            {
                $finalTest = $reg_array[1][$municipality->id . '_finalTest'] / $reg_array[1][$municipality->id . '_countOrgFinal'];
                $finalTest_total += $finalTest;
                $finalTest_count++;
            } //итоговый тест
            else {
                $finalTest = 0;
            }
            if (array_key_exists($municipality->id . '_finalTest_1st', $reg_array[1])) {
                $finalTest_1st = $reg_array[1][$municipality->id . '_finalTest_1st'];
                $finalTest_1st_total += $finalTest_1st;
            } else {
                $finalTest_1st = 0;
            }
            if (array_key_exists($municipality->id . '_finalTest_2st', $reg_array[1])) {
                $finalTest_2st = $reg_array[1][$municipality->id . '_finalTest_2st'];
                $finalTest_2st_total += $finalTest_2st;
            } else {
                $finalTest_2st = 0;
            }
            if (array_key_exists($municipality->id . '_trainingCompletedAll', $reg_array[1])) {
                $trainingCompletedAll = $reg_array[1][$municipality->id . '_trainingCompletedAll'];
                $trainingCompletedAll_total += $trainingCompletedAll;
            } else {
                $trainingCompletedAll = 0;
            }
            if (array_key_exists($municipality->id . '_trainingCompletedParent', $reg_array[1])) {
                $trainingCompletedParent = $reg_array[1][$municipality->id . '_trainingCompletedParent'];
                $trainingCompletedParent_total += $trainingCompletedParent;
            } else {
                $trainingCompletedParent = 0;
            }
            if (array_key_exists($municipality->id . '_trainingCompletedChild', $reg_array[1])) {
                $trainingCompletedChild = $reg_array[1][$municipality->id . '_trainingCompletedChild'];
                $trainingCompletedChild_total += $trainingCompletedChild;
            } else {
                $trainingCompletedChild = 0;
            }
            /*(END)*/
            ?>

            <tr class="text-center <?php
            if ($count == 1) {
                echo 'prepend-reg';
            } ?>">
                <td><?= $count++ ?></td>
                <td><?= $municipality->name ?></td>
                <td><?= $startWork ?></td>
                <td><?= $countOrgStart ?></td>
                <td><?= $people ?></td>
                <td><?= $parent ?></td>
                <td><?= $child ?></td>
                <td><?= round($inputTest, 1) ?></td>
                <td><?= $theme1 ?></td>
                <td><?= $theme2 ?></td>
                <td><?= $theme3 ?></td>
                <td><?= $theme4 ?></td>
                <td><?= $theme5 ?></td>
                <td><?= $independentWork ?></td>
                <td><?= round($finalTest, 1) ?></td>
                <td><?= $finalTest_1st ?></td>
                <td><?= $finalTest_2st ?></td>
                <td><?= $trainingCompletedAll ?></td>
                <td><?= $trainingCompletedParent ?></td>
                <td><?= $trainingCompletedChild ?></td>
            </tr>
            <?php
        }
        ?>
        <tr class="text-center font-weight-bold bg-warning">
            <td colspan="2">Итого</td>
            <td><?= $startWork_total ?></td>
            <td><?= $countOrgStart_total ?></td>
            <td><?= $people_total ?></td>
            <td><?= $parent_total ?></td>
            <td><?= $child_total ?></td>
            <td><?= ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?></td>
            <td><?= $theme1_total ?></td>
            <td><?= $theme2_total ?></td>
            <td><?= $theme3_total ?></td>
            <td><?= $theme4_total ?></td>
            <td><?= $theme5_total ?></td>
            <td><?= $independentWork_total ?></td>
            <td><?= ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?></td>
            <td><?= $finalTest_1st_total ?></td>
            <td><?= $finalTest_2st_total ?></td>
            <td><?= $trainingCompletedAll_total ?></td>
            <td><?= $trainingCompletedParent_total ?></td>
            <td><?= $trainingCompletedChild_total ?></td>
        </tr>
        </tbody>
    </table>
    <input type="button" class="btn btn-warning btn-block table2excel mb-3 mt-3"
           title="Вы можете скачать в формате Excel" value="Скачать в Excel" id="pechat222">
    <?php
} //по региону

elseif (!empty($fed_org)) //по федеральному
{
    if ($post['title'] == 2022) {
        $fed_array = $user_model->reportNew($regions[0]['district_id'], 1, 5);
    } elseif ($post['title'] == 2021) {
        $fed_array = $user_model21->reportNew($regions[0]['district_id'], 1, 5);
    } elseif ($post['title'] == 2020) {
        $fed_array = $user_model20->reportNew($regions[0]['district_id'], 1, 5);
    }

    //print_r($fed_array);die();

    ?>
    <table id="tableId" class="table table-hover table-bordered table-striped mt-3 table2excel_with_colors">
        <thead>
        <tr class="text-center">
            <th rowspan="2">№</th>
            <th rowspan="2">Субъект федерации</th>
            <th rowspan="2" style="max-width: 120px;">Приступили к обучению (1-да, 0-нет)</th>
            <th rowspan="2" style="max-width: 160px;">Количество муниципальных образований приступивших к работе
            <th rowspan="2" style="max-width: 160px;">Количество образовательных организаций приступивших к работе
            </th>

            <th colspan="3" style="max-width: 180px;">Количество зарегистрировавшихся человек</th>
            <th colspan="11">Количество человек прошедших обучение</th>
            <th colspan="3">Завершили обучение</th>
        </tr class="text-center">
        <tr class="text-center">
            <th style="max-width: 122px;">Всего</th>
            <th style="max-width: 122px;">Взрослых</th>
            <th style="max-width: 122px;">Детей</th>

            <th style="max-width: 150px;">Входной тест (среднее значение %)</th>
            <?
            foreach ($themes as $them) {
                ?>
                <th style="max-width: 120px;"><?= $them->short_name ?></th>
                <?
            } ?>
            <th style="max-width: 153px;">Самостоятельная работа</th>
            <th style="max-width: 122px;">Итоговый тест (среднее значение %)</th>
            <th style="max-width: 150px;">Итоговый тест пройден с 1-ого раза</th>
            <th style="max-width: 150px;">Итоговый тест пройден со 2-ого раза или более</th>
            <th style="max-width: 150px;">Обучение не завершено</th>

            <th style="max-width: 122px;">Всего</th>
            <th style="max-width: 122px;">Взрослых</th>
            <th style="max-width: 122px;">Детей</th>
        </tr>
        </thead>
        <tbody>
        <?
        $count = 1;
        $startWork_total = 0;
        $countOrgStart_total = 0;
        $countMunStart_total = 0;
        $people_total = 0;
        $parent_total = 0;
        $child_total = 0;
        $inputTest_total = 0;
        $inputTest_count = 0;
        $theme1_total = 0;
        $theme2_total = 0;
        $theme3_total = 0;
        $theme4_total = 0;
        $theme5_total = 0;
        $independentWork_total = 0;
        $finalTest_total = 0;
        $finalTest_count = 0;
        $finalTest_1st_total = 0;
        $finalTest_2st_total = 0;
        $trainingCompleted_total = 0;

        $trainingCompletedAll_total = 0;
        $trainingCompletedParent_total = 0;
        $trainingCompletedChild_total = 0;
        $trainingNotCompl_total = 0;

        foreach ($regions as $region) {
            /**/
            if (array_key_exists($region->id . '_countOrgStart', $fed_array[1])) {
                $countOrgStart = $fed_array[1][$region->id . '_countOrgStart']; //Количество образовательных организаций приступивших к работе
                $countOrgStart_total += $countOrgStart;
                $startWork = 1; //Приступили к обучению (1-да, 0-нет)
                $startWork_total++;
                $countMunStart = $fed_array[1][$region->id . '_countMunStart']; //Количество муниципальных в регионе приступивших к работе
                $countMunStart_total += $countMunStart;
            } else {
                $countOrgStart = 0;
                $startWork = 0;
                $countMunStart = 0;
            }

            if (array_key_exists($region->id . '_training_id_1',
                    $fed_array[0]) || array_key_exists($region->id . '_training_id_2',
                    $fed_array[0])) { //Количество зарегистрировавшихся человек (всего)
                $people = $fed_array[0][$region->id . '_training_id_1'] + $fed_array[0][$region->id . '_training_id_2'];
                $people_total += $people;
            } else {
                $people = 0;
            }

            if (array_key_exists($region->id . '_training_id_2',
                $fed_array[0])) //Количество зарегистрировавшихся человек (взрослых)
            {
                $parent = $fed_array[0][$region->id . '_training_id_2'];
                $parent_total += $parent;
            } else {
                $parent = 0;
            }
            if (array_key_exists($region->id . '_training_id_1',
                $fed_array[0])) //Количество зарегистрировавшихся человек (детей)
            {
                $child = $fed_array[0][$region->id . '_training_id_1'];
                $child_total += $child;
            } else {
                $child = 0;
            }

            if (array_key_exists($region->id . '_regionId', $fed_array[1])) //входной тест
            {
                $inputTest = $fed_array[1][$region->id . '_regionId'];
                $inputTest_total += $inputTest;
                $inputTest_count++;
            } //входной тест
            else {
                $inputTest = 0;
            }
            //print_r($fed_array);die();
            if (array_key_exists($region->id . '_theme1', $fed_array[1])) {
                $theme1 = $fed_array[1][$region->id . '_theme1'];
                $theme1_total += $theme1;
            } else {
                $theme1 = 0;
            }
            if (array_key_exists($region->id . '_theme2', $fed_array[1])) {
                $theme2 = $fed_array[1][$region->id . '_theme2'];
                $theme2_total += $theme2;
            } else {
                $theme2 = 0;
            }
            if (array_key_exists($region->id . '_theme3', $fed_array[1])) {
                $theme3 = $fed_array[1][$region->id . '_theme3'];
                $theme3_total += $theme3;
            } else {
                $theme3 = 0;
            }
            if (array_key_exists($region->id . '_theme4', $fed_array[1])) {
                $theme4 = $fed_array[1][$region->id . '_theme4'];
                $theme4_total += $theme4;
            } else {
                $theme4 = 0;
            }
            if (array_key_exists($region->id . '_theme5', $fed_array[1])) {
                $theme5 = $fed_array[1][$region->id . '_theme5'];
                $theme5_total += $theme5;
            } else {
                $theme5 = 0;
            }
            if (array_key_exists($region->id . '_independentWork', $fed_array[1])) {
                $independentWork = $fed_array[1][$region->id . '_independentWork'];
                $independentWork_total += $independentWork;
            } else {
                $independentWork = 0;
            }
            //
            if (array_key_exists($region->id . '_regionId2', $fed_array[1])) //итоговый тест
            {
                $finalTest = $fed_array[1][$region->id . '_regionId2'];
                $finalTest_total += $finalTest;
                $finalTest_count++;
            } //итоговый тест
            else {
                $finalTest = 0;
            }
            //
            if (array_key_exists($region->id . '_finalTest_1st', $fed_array[1])) {
                $finalTest_1st = $fed_array[1][$region->id . '_finalTest_1st'];
                $finalTest_1st_total += $finalTest_1st;
            } else {
                $finalTest_1st = 0;
            }
            if (array_key_exists($region->id . '_finalTest_2st', $fed_array[1])) {
                $finalTest_2st = $fed_array[1][$region->id . '_finalTest_2st'];
                $finalTest_2st_total += $finalTest_2st;
            } else {
                $finalTest_2st = 0;
            }
            if (array_key_exists($region->id . '_trainingCompletedAll', $fed_array[1])) {
                $trainingCompletedAll = $fed_array[1][$region->id . '_trainingCompletedAll'];
                $trainingCompletedAll_total += $trainingCompletedAll;
            } else {
                $trainingCompletedAll = 0;
            }
            if (array_key_exists($region->id . '_trainingCompletedParent', $fed_array[1])) {
                $trainingCompletedParent = $fed_array[1][$region->id . '_trainingCompletedParent'];
                $trainingCompletedParent_total += $trainingCompletedParent;
            } else {
                $trainingCompletedParent = 0;
            }
            if (array_key_exists($region->id . '_trainingCompletedChild', $fed_array[1])) {
                $trainingCompletedChild = $fed_array[1][$region->id . '_trainingCompletedChild'];
                $trainingCompletedChild_total += $trainingCompletedChild;
            } else {
                $trainingCompletedChild = 0;
            }

            $trainingNotCompl = min($theme1, $theme2, $theme3, $theme4, $theme5,
                    $independentWork) - $trainingCompletedAll;
            $trainingNotCompl_total += $trainingNotCompl;
            /*(END)*/
            ?>

            <tr class="text-center <?
            if ($count == 1) {
                echo 'prepend-fed';
            } ?>">
                <td><?= $count++ ?></td>
                <td><?= $region->name ?></td>
                <td><?= $startWork ?></td>
                <td><?= $countMunStart ?></td>
                <td><?= $countOrgStart ?></td>
                <td><?= $people ?></td>
                <td><?= $parent ?></td>
                <td><?= $child ?></td>
                <td><?= round($inputTest, 1) ?></td>
                <td><?= $theme1 ?></td>
                <td><?= $theme2 ?></td>
                <td><?= $theme3 ?></td>
                <td><?= $theme4 ?></td>
                <td><?= $theme5 ?></td>
                <td><?= $independentWork ?></td>
                <td><?= round($finalTest, 1) ?></td>
                <td><?= $finalTest_1st ?></td>
                <td><?= $finalTest_2st ?></td>
                <td><?= $trainingNotCompl ?></td>
                <td><?= $trainingCompletedAll ?></td>
                <td><?= $trainingCompletedParent ?></td>
                <td><?= $trainingCompletedChild ?></td>
            </tr>
            <?
        }
        ?>
        <tr class="text-center font-weight-bold bg-warning">
            <td colspan="2">Итого</td>
            <td><?= $startWork_total ?></td>
            <td><?= $countMunStart_total ?></td>
            <td><?= $countOrgStart_total ?></td>
            <td><?= $people_total ?></td>
            <td><?= $parent_total ?></td>
            <td><?= $child_total ?></td>
            <td><?= ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?></td>
            <td><?= $theme1_total ?></td>
            <td><?= $theme2_total ?></td>
            <td><?= $theme3_total ?></td>
            <td><?= $theme4_total ?></td>
            <td><?= $theme5_total ?></td>
            <td><?= $independentWork_total ?></td>
            <td><?= ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?></td>
            <td><?= $finalTest_1st_total ?></td>
            <td><?= $finalTest_2st_total ?></td>
            <td><?= $trainingNotCompl_total ?></td>
            <td><?= $trainingCompletedAll_total ?></td>
            <td><?= $trainingCompletedParent_total ?></td>
            <td><?= $trainingCompletedChild_total ?></td>
        </tr>
        </tbody>
    </table>
    <input type="button" class="btn btn-warning btn-block table2excel mb-3 mt-3"
           title="Вы можете скачать в формате Excel" value="Скачать в Excel" id="pechat222">
    <?
} //по федеральному
?>

    <script type="text/javascript">
        var org = '<?php echo $org;?>';
        var mun_org = '<?php echo $mun_org;?>';
        var reg_org = '<?php echo $reg_org;?>';
        var fed_org = '<?php echo $fed_org;?>';

        if (org === '1')
        {
            col1 = '<?php echo ($count < 2) ? '0' : round($input_test_total * 10 / ($count - 1), 1) . '%';?>';
            col2 = '<?php echo $theme1_total;?>';
            col3 = '<?php echo $theme2_total;?>';
            col4 = '<?php echo $theme3_total;?>';
            col5 = '<?php echo $theme4_total;?>';
            col6 = '<?php echo $theme5_total;?>';
            col7 = '<?php echo $independent_work_total;?>';
            col8 = '<?php echo ($final_test_count_total == 0) ? '0' : round($final_test_total * 10 / $final_test_count_total,
                    1) . '%';?>';
            col9 = '<?php echo $final_test_1st_total;?>';
            col10 = '<?php echo $final_test_2st_total;?>';
            col11 = '<?php echo $training_completed_total;?>';
        }
        else if (mun_org === '1')
        {
            col1 = '<?php echo $start_work_total ?>';
            col2 = '<?php echo $people_total ?>';
            col3 = '<?php echo $parent_total ?>';
            col4 = '<?php echo $child_total ?>';
            col5 = '<?php echo ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?>';
            col6 = '<?php echo $theme1_total ?>';
            col7 = '<?php echo $theme2_total ?>';
            col8 = '<?php echo $theme3_total ?>';
            col9 = '<?php echo $theme4_total ?>';
            col10 = '<?php echo $theme5_total ?>';
            col11 = '<?php echo $independentWork_total ?>';
            col12 = '<?php echo ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?>';
            col13 = '<?php echo $finalTest_1st_total ?>';
            col14 = '<?php echo $finalTest_2st_total ?>';
            col15 = '<?php echo $trainingCompletedAll_total ?>';
            col16 = '<?php echo $trainingCompletedParent_total ?>';
            col17 = '<?php echo $trainingCompletedChild_total ?>';
        }
        else if (reg_org === '1')
        {
            col1 = '<?php echo $startWork_total ?>';
            col2 = '<?php echo $countOrgStart_total ?>';
            col3 = '<?php echo $people_total ?>';
            col4 = '<?php echo $parent_total ?>';
            col5 = '<?php echo $child_total ?>';
            col6 = '<?php echo ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?>';
            col7 = '<?php echo $theme1_total ?>';
            col8 = '<?php echo $theme2_total ?>';
            col9 = '<?php echo $theme3_total ?>';
            col10 = '<?php echo $theme4_total ?>';
            col11 = '<?php echo $theme5_total ?>';
            col12 = '<?php echo $independentWork_total ?>';
            col13 = '<?php echo ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?>';
            col14 = '<?php echo $finalTest_1st_total ?>';
            col15 = '<?php echo $finalTest_2st_total ?>';
            col16 = '<?php echo $trainingCompletedAll_total ?>';
            col17 = '<?php echo $trainingCompletedParent_total ?>';
            col18 = '<?php echo $trainingCompletedChild_total ?>';
        }
        else if (fed_org === '1')
        {
            col1 = '<?php echo $startWork_total ?>';
            col2 = '<?php echo $countMunStart_total ?>';
            col3 = '<?php echo $countOrgStart_total ?>';
            col4 = '<?php echo $people_total ?>';
            col5 = '<?php echo $parent_total ?>';
            col6 = '<?php echo $child_total ?>';
            col7 = '<?php echo ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?>';
            col8 = '<?php echo $theme1_total ?>';
            col9 = '<?php echo $theme2_total ?>';
            col10 = '<?php echo $theme3_total ?>';
            col11 = '<?php echo $theme4_total ?>';
            col12 = '<?php echo $theme5_total ?>';
            col13 = '<?php echo $independentWork_total ?>';
            col14 = '<?php echo ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?>';
            col15 = '<?php echo $finalTest_1st_total ?>';
            col16 = '<?php echo $finalTest_2st_total ?>';
            col17 = '<?php echo $trainingNotCompl_total ?>';
            col18 = '<?php echo $trainingCompletedAll_total ?>';
            col19 = '<?php echo $trainingCompletedParent_total ?>';
            col20 = '<?php echo $trainingCompletedChild_total ?>';
        }
    </script>

<?php
$script = <<< JS
    $(".beforeload").click(function() {
      $(".beforeload").css('display','none');
      $(".load").css('display','block');
    });

    $("#pechat222").click(function () {
    var table = $('#tableId');
        if (table && table.length) {
            var preserveColors = (table.hasClass('table2excel_with_colors') ? true : false);
            $(table).table2excel({
                exclude: ".noExl",
                name: "Excel Document Name",
                filename: "Отчет.xls",
                fileext: ".xls",
                exclude_img: true,
                exclude_links: true,
                exclude_inputs: true,
                preserveColors: preserveColors
            });
        }
    });

    if (org==='1'){
        $('.prepend-org').before(
            '<tr class="text-center font-weight-bold bg-warning">' +
            '<td colspan="4">Итого</td>' +
            '<td>'+col1+'</td>' +
            '<td>'+col2+'</td>' +
            '<td>'+col3+'</td>' +
            '<td>'+col4+'</td>' +
            '<td>'+col5+'</td>' +
            '<td>'+col6+'</td>' +
            '<td>'+col7+'</td>' +
            '<td>'+col8+'</td>' +
            '<td>'+col9+'</td>' +
            '<td>'+col10+'</td>' +
            '<td>'+col11+'</td>' +
            '</tr>'
        );
    }
    else if (mun_org === '1'){
        $('.prepend-mun').before(
            '<tr class="text-center font-weight-bold bg-warning">' +
            '<td colspan="2">Итого</td>' +
            '<td>'+col1+'</td>' +
            '<td>'+col2+'</td>' +
            '<td>'+col3+'</td>' +
            '<td>'+col4+'</td>' +
            '<td>'+col5+'</td>' +
            '<td>'+col6+'</td>' +
            '<td>'+col7+'</td>' +
            '<td>'+col8+'</td>' +
            '<td>'+col9+'</td>' +
            '<td>'+col10+'</td>' +
            '<td>'+col11+'</td>' +
            '<td>'+col12+'</td>' +
            '<td>'+col13+'</td>' +
            '<td>'+col14+'</td>' +
            '<td>'+col15+'</td>' +
            '<td>'+col16+'</td>' +
            '<td>'+col17+'</td>' +
            '</tr>'
        );
    }
    else if (reg_org === '1'){
        $('.prepend-reg').before(
            '<tr class="text-center font-weight-bold bg-warning">' +
            '<td colspan="2">Итого</td>' +
            '<td>'+col1+'</td>' +
            '<td>'+col2+'</td>' +
            '<td>'+col3+'</td>' +
            '<td>'+col4+'</td>' +
            '<td>'+col5+'</td>' +
            '<td>'+col6+'</td>' +
            '<td>'+col7+'</td>' +
            '<td>'+col8+'</td>' +
            '<td>'+col9+'</td>' +
            '<td>'+col10+'</td>' +
            '<td>'+col11+'</td>' +
            '<td>'+col12+'</td>' +
            '<td>'+col13+'</td>' +
            '<td>'+col14+'</td>' +
            '<td>'+col15+'</td>' +
            '<td>'+col16+'</td>' +
            '<td>'+col17+'</td>' +
            '<td>'+col18+'</td>' +
            '</tr>'
        );
    }
    else if (fed_org === '1'){
        $('.prepend-fed').before(
            '<tr class="text-center font-weight-bold bg-warning">' +
            '<td colspan="2">Итого</td>' +
            '<td>'+col1+'</td>' +
            '<td>'+col2+'</td>' +
            '<td>'+col3+'</td>' +
            '<td>'+col4+'</td>' +
            '<td>'+col5+'</td>' +
            '<td>'+col6+'</td>' +
            '<td>'+col7+'</td>' +
            '<td>'+col8+'</td>' +
            '<td>'+col9+'</td>' +
            '<td>'+col10+'</td>' +
            '<td>'+col11+'</td>' +
            '<td>'+col12+'</td>' +
            '<td>'+col13+'</td>' +
            '<td>'+col14+'</td>' +
            '<td>'+col15+'</td>' +
            '<td>'+col16+'</td>' +
            '<td>'+col17+'</td>' +
            '<td>'+col18+'</td>' +
            '<td>'+col19+'</td>' +
            '<td>'+col20+'</td>' +
            '</tr>'
        );
    }
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>