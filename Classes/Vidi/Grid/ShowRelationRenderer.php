<?php
namespace Visol\EasyvoteEducation\Vidi\Grid;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Fab\Vidi\Tca\Tca;

/**
 * Class rendering relation
 */
class ShowRelationRenderer extends \Fab\Vidi\Grid\ColumnRendererAbstract
{

    /**
     * Render a representation of the relation on the GUI.
     *
     * @return string
     */
    public function render()
    {

        $result = '';

        // Get TCA table service.
        $table = Tca::table($this->object);

        // Get label of the foreign table.
        $foreignLabelField = $this->getForeignTableLabelField($this->fieldName);

        if ($table->field($this->fieldName)->hasOne()) {
            $foreignObject = $this->object[$this->fieldName];
            if ($foreignObject) {
                $result = $foreignObject[$foreignLabelField];
            }
        }

        return $result;
    }

    /**
     * Return the label field of the foreign table.
     *
     * @param string $fieldName
     * @return string
     */
    protected function getForeignTableLabelField($fieldName)
    {

        // Get TCA table service.
        $table = Tca::table($this->object);

        // Compute the label of the foreign table.
        $relationDataType = $table->field($fieldName)->relationDataType();
        return Tca::table($relationDataType)->getLabelField();
    }

}
