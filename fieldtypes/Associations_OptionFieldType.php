<?php
namespace Craft;


class Associations_OptionFieldType extends BaseFieldType
{
	// Public Methods
	// =========================================================================

	/**
	 * @inheritDoc IComponentType::getName()
	 *
	 * @return string
	 */
	public function getName()
	{
		return Craft::t('Associations with Option');
	}

	/**
	 * @inheritDoc IFieldType::defineContentAttribute()
	 *
	 * @return mixed
	 */
	public function defineContentAttribute()
	{
		return AttributeType::Mixed;
	}

	/**
	 * @inheritDoc ISavableComponentType::getSettingsHtml()
	 *
	 * @return string|null
	 */
	public function getSettingsHtml()
	{
		foreach (craft()->fields->getAllFields('id') as $id=>$field)
		{
            $ft = craft()->fields->getFieldType($field->type);
            if (is_subclass_of($ft, 'Craft\BaseOptionsFieldType'))
            {
                $fields[] = array('label' => $field->name, 'value' => $id);
            }
		}
        
        $input = craft()->templates->render('_includes/forms/checkboxSelect', array(
            'id'           => 'fields',
            'name'         => 'fields',
            'options'      => $fields,
            'values'       => $this->getSettings()->fields
        ));
        $settingsHtml = craft()->templates->render('_includes/forms/field', array(
            'label'        => 'Display options from fields',
            'input'        => $input
        ));
        
        return $settingsHtml;
	}

	/**
	 * @inheritDoc IFieldType::getInputHtml()
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return string
	 */
	public function getInputHtml($name, $value)
	{
		$input = '<input type="hidden" name="'.$name.'" value="">';

		$tableHtml = $this->_getInputHtml($name, $value, false);

		if ($tableHtml)
		{
			$input .= $tableHtml;
		}

		return $input;
	}

	/**
	 * @inheritDoc IFieldType::prepValue()
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function prepValue($value)
	{
        return $value;
	}

	/**
	 * @inheritDoc IFieldType::getStaticHtml()
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function getStaticHtml($value)
	{
		return $this->_getInputHtml(StringHelper::randomString(), $value, true);
	}

	// Protected Methods
	// =========================================================================

	/**
	 * @inheritDoc BaseSavableComponentType::defineSettings()
	 *
	 * @return array
	 */
	protected function defineSettings()
	{
		return array(
			'fields' => AttributeType::Mixed
		);
	}

	/**
	 * @inheritDoc ISavableComponentType::prepSettings()
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function prepSettings($settings)
	{

		return $settings;
	}

	// Private Methods
	// =========================================================================

	/**
	 * Returns the field's input HTML.
	 *
	 * @param string $name
	 * @param mixed  $value
	 * @param bool  $static
	 *
	 * @return string
	 */
	private function _getInputHtml($name, $value, $static)
	{
		$options = [];
        $fields = [];
        if ($this->getSettings()->fields=='*')
        {
            foreach (craft()->fields->getAllFields('id') as $id=>$field)
    		{
                $ft = craft()->fields->getFieldType($field->type);
                if (is_subclass_of($ft, 'Craft\BaseOptionsFieldType'))
                {
                    $fields[$id] = $field->name;
                }
    		}
        }
        else
        {
            foreach ($this->getSettings()->fields as $fieldId)
            {
                $field = craft()->fields->getFieldById($fieldId);
                $fields[$fieldId] = $field->name;
            }
        }
        
        foreach ($fields as $fieldId=>$fieldName)
        {
            if (count($fields)>1)
            {
                $options['group_'.$fieldId] = ['optgroup' => $fieldName];
            }
            $optionsList = craft()->fields->getFieldById($fieldId)->getFieldType()->getSettings()->options;
            foreach ($optionsList as $option)
            {
                $options[$option['value']] = $option['label'];
            }
        }

        $columns = [
            'orig' => [
                'heading' => Craft::t('Option'), 
                'handle' => 'orig', 
                'type' => 'select',
                'options' => $options
            ],
            'assoc' => [
                'heading' => Craft::t('Association'), 
                'handle' => 'assoc', 
                'type' => 'singleline'
            ]
        ];

		$id = craft()->templates->formatInputId($name);

		return craft()->templates->render('_includes/forms/editableTable', array(
			'id'     => $id,
			'name'   => $name,
			'cols'   => $columns,
			'rows'   => $value,
			'static' => $static
		));
	}
}
