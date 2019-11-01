<?php

namespace frontend\widgets\MenuCalendar;

use yii\helpers\Json;
use yii\web\View;
use yii2fullcalendar\CoreAsset;
use yii2fullcalendar\ThemeAsset;
use yii2fullcalendar\yii2fullcalendar;

class MenuCalendar extends yii2fullcalendar
{
    public $stickyEvents = true;
    public $allDayDefault = true;

    /**
     * Registers the FullCalendar javascript assets and builds the requiered js  for the widget and the related events
     */
    protected function registerPlugin()
    {
        $id = $this->options['id'];
        $view = $this->getView();

        /** @var \yii\web\AssetBundle $assetClass */
        $assets = CoreAsset::register($view);

        //by default we load the jui theme, but if you like you can set the theme to false and nothing gets loaded....
        if ($this->theme == true) {
            ThemeAsset::register($view);
        }

        if (isset($this->options['lang'])) {
            $assets->language = $this->options['lang'];
        }

        if ($this->googleCalendar) {
            $assets->googleCalendar = $this->googleCalendar;
        }

        $js = array();

        if ($this->ajaxEvents != NULL) {
            $this->clientOptions['events'] = $this->ajaxEvents;
        }

        if (is_array($this->header) && isset($this->clientOptions['header'])) {
            $this->clientOptions['header'] = array_merge($this->header, $this->clientOptions['header']);
        } else {
            $this->clientOptions['header'] = $this->header;
        }

        if (isset($this->defaultView) && !isset($this->clientOptions['defaultView'])) {
            $this->clientOptions['defaultView'] = $this->defaultView;
        }

        // clear existing calendar display before rendering new fullcalendar instance
        // this step is important when using the fullcalendar widget with pjax
        $js[] = "var loading_container = jQuery('#$id .fc-loading');"; // take backup of loading container
        $js[] = "jQuery('#$id').empty().append(loading_container);"; // remove/empty the calendar container and append loading container bakup

        $cleanOptions = $this->getClientOptions();
        $js[] = "jQuery('#$id').fullCalendar($cleanOptions);";

        //sticky
        if (count($this->events) > 0) {
            foreach ($this->events AS $event) {
                $jsonEvent = Json::encode($event);
                $isSticky = $this->stickyEvents;
                $js[] = "jQuery('#$id').fullCalendar('renderEvent',$jsonEvent,$isSticky);";
            }
        }

        $view->registerJs(implode("\n", $js), View::POS_READY);
    }
}