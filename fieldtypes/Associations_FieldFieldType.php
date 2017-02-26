<?php
namespace Craft;


class Associations_FieldFieldType extends BaseFieldType
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
		return Craft::t('Associations with Field');
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
		foreach (craft()->fields->getAllGroups('id') as $id=>$name)
		{
			$groups[] = array('label' => $name, 'value' => $id);
		}
        
        $input = craft()->templates->render('_includes/forms/checkboxSelect', array(
            'id'           => 'groups',
            'name'         => 'groups',
            'options'      => $groups,
            'values'       => $this->getSettings()->groups
        ));
        $settingsHtml = craft()->templates->render('_includes/forms/field', array(
            'label'        => 'Display fields from groups',
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
			'groups' => AttributeType::Mixed
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
        if ($this->getSettings()->groups=='*')
        {
            $allGroups = craft()->fields->getAllGroups('id');
            foreach ($allGroups as $id=>$group)
            {
                $groups[] = $id;
            }
        }
        else
        {
            $groups = $this->getSettings()->groups;
        }
        
        foreach ($groups as $groupId)
        {
            if (count($groups)>1)
            {
                $group = craft()->fields->getGroupById($groupId);
                $options['group_'.$group->id] = ['optgroup' => $group->name];
            }
            $fields = craft()->fields->getFieldsByGroupId($groupId, 'handle');
            foreach ($fields as $handle=>$field)
            {
                $options[$handle] = $field->name;
            }
        }

        $columns = [
            'orig' => [
                'heading' => Craft::t('Field'), 
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
