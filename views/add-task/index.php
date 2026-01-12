<?php 
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var $model app\models\AddTaskForm */
/** @var $categories app\models\Category[] */

$categoryItems = ArrayHelper::map($categories, 'id', 'title');
?>

<link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.9/dist/css/autoComplete.min.css">

<main class="main-content main-content--center container">
    <div class="add-task-form regular-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
      ]); ?>

      <h3 class="head-main head-main">Публикация нового задания</h3>      
      <div class="form-group">
        <?= $form->field($model, 'title')->textInput()->label('Опишите суть работы') ?>
      </div>

      <div class="form-group">
        <?= $form->field($model, 'description')->textarea()->label('Подробности задания') ?>
      </div>

      <div class="form-group">
        <?= $form->field($model, 'category_id')->dropDownList(
          $categoryItems, ['prompt' => 'Выберите категорию']
        )->label('Категория') ?>
      </div>

      <div class="form-group">
      <?= $form->field($model, 'location')
        ->textInput(['class' => 'location-icon', 'id' => 'location-input'])
        ->label('Локация') ?>
    </div>
    <?= $form->field($model, 'latitude')->hiddenInput(['id' => 'location-lat'])->label(false) ?>
    <?= $form->field($model, 'longitude')->hiddenInput(['id' => 'location-lng'])->label(false) ?>


      <div class="half-wrapper">
        <div class="form-group">
        <?= $form->field($model, 'cost')->textInput(['class' => 'budget-icon'])->label('Бюджет') ?>  
        </div>
        <div class="form-group">
        <?= $form->field($model, 'date_end')->input('date')->label('Срок исполнения') ?>  
        </div>
      </div>

      <p class="form-label">Файлы</p>
      <div class="form-group">
          <?= $form->field($model, 'files')->fileInput((['multiple' => true]))->label(false) ?>
      </div>
          <input type="submit" class="button button--blue" value="Опубликовать">
        <?php ActiveForm::end() ?>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.9/dist/autoComplete.min.js"></script>
<script>
  
  const autoCompleteJS = new autoComplete({
    selector: "#location-input",
    threshold: 2,
    debounce: 300,

    data: {
      src: async (query) => {
        const response = await fetch(`/geocoder/search?query=${encodeURIComponent(query)}`);
        return await response.json();
      },
      keys: ["label"]
    },

    resultItem: {
      highlight: true
    },

    events: {
      input: {
        selection: (event) => {
          const selection = event.detail.selection.value;

          autoCompleteJS.input.value = selection.label;
          document.getElementById("location-lat").value = selection.latitude;
          document.getElementById("location-lng").value = selection.longitude;
        }
      }
    }
  });
</script>
