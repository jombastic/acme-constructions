<?php

namespace Winter\Pages\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Winter\Pages\Classes\MenuItem;

/**
 * Menu items widget.
 *
 * @package winter\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class MenuItems extends FormWidgetBase
{
    protected $typeListCache = false;
    protected $typeInfoCache = [];

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'menuitems';

    public $addSubitemLabel = 'winter.pages::lang.menu.add_subitem';

    public $noRecordsMessage = 'winter.pages::lang.menu.no_records';

    public $titleRequiredMessage = 'winter.pages::lang.menuitem.title_required';

    public $referenceRequiredMessage = 'winter.pages::lang.menuitem.reference_required';

    public $urlRequiredMessage = 'winter.pages::lang.menuitem.url_required';

    public $cmsPageRequiredMessage = 'winter.pages::lang.menuitem.cms_page_required';

    public $newItemTitle = 'winter.pages::lang.menuitem.new_item';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('menuitems');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $menuItem = new MenuItem();

        $this->vars['itemProperties'] = json_encode($menuItem->fillable);
        $this->vars['items'] = $this->model->{$this->fieldName};

        $emptyItem = new MenuItem();
        $emptyItem->title = trans($this->newItemTitle);
        $emptyItem->type = 'url';
        $emptyItem->url = '/';

        $this->vars['emptyItem'] = $emptyItem;

        $widgetConfig = $this->makeConfig('~/plugins/winter/pages/classes/menuitem/fields.yaml');
        $widgetConfig->model = $menuItem;
        $widgetConfig->alias = $this->alias . 'MenuItem';

        $this->vars['itemFormWidget'] = $this->makeWidget('Backend\Widgets\Form', $widgetConfig);
    }

    /**
     * {@inheritDoc}
     */
    protected function loadAssets()
    {
        $this->addJs('js/menu-items-editor.js', 'Winter.Pages');
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveValue($value)
    {
        return strlen($value) ? $value : null;
    }

    //
    // Methods for the internal use
    //

    /**
     * Returns the item reference description.
     * @param \Winter\Pages\Classes\MenuItem $item Specifies the menu item
     * @return string
     */
    protected function getReferenceDescription($item)
    {
        if ($this->typeListCache === false) {
            $this->typeListCache = $item->getTypeOptions();
        }

        if (!isset($this->typeInfoCache[$item->type])) {
            $this->typeInfoCache[$item->type] = MenuItem::getTypeInfo($item->type);
        }

        if (isset($this->typeInfoCache[$item->type])) {
            $result = trans($this->typeListCache[$item->type]);

            if ($item->type !== 'url') {
                if (isset($this->typeInfoCache[$item->type]['references'])) {
                    $result .= ': ' . $this->findReferenceName($item->reference, $this->typeInfoCache[$item->type]['references']);
                }
            } else {
                $result .= ': ' . $item->url;
            }
        } else {
            $result = trans('winter.pages::lang.menuitem.unknown_type');
        }

        return $result;
    }

    protected function findReferenceName($search, $typeOptionList)
    {
        $iterator = function ($optionList, $path) use ($search, &$iterator) {
            foreach ($optionList as $reference => $info) {
                if ($reference == $search) {
                    $result = $this->getMenuItemTitle($info);

                    return strlen($path) ? $path . ' / ' . $result : $result;
                }

                if (is_array($info) && isset($info['items'])) {
                    $result = $iterator($info['items'], $path . ' / ' . $this->getMenuItemTitle($info));

                    if (strlen($result)) {
                        return strlen($path) ? $path . ' / ' . $result : $result;
                    }
                }
            }
        };

        $result = $iterator($typeOptionList, null);
        if (!strlen($result)) {
            $result = trans('winter.pages::lang.menuitem.unnamed');
        }

        $result = preg_replace('|^\s+\/|', '', $result);

        return $result;
    }

    protected function getMenuItemTitle($itemInfo)
    {
        if (is_array($itemInfo)) {
            if (!array_key_exists('title', $itemInfo) || !strlen($itemInfo['title'])) {
                return trans('winter.pages::lang.menuitem.unnamed');
            }

            return $itemInfo['title'];
        }

        return strlen($itemInfo) ? $itemInfo : trans('winter.pages::lang.menuitem.unnamed');
    }
}
