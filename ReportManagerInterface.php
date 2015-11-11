<?php
namespace reportmanager;

interface ReportManagerInterface
{
    /**
     * Method to return dataProvider.
     * This dataProvider will be used to generate query
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params);

    /**
     * Method to return a list of properties which can be used by the ReportManager
     * to generate reports
     *
     * The format of the return array is following:
     * <code>
     * <?php
     * [
     *     [
     *         'attribute' => 'attribute_name',
     *         'label' => 'Attribute Label',
     *         'operations' => ['select', 'group',],
     *         'values' => [
     *             'value1' => 'label1',
     *             ...
     *         ],
     *     ],
     *     [
     *         ...
     *     ],
     * ];
     * </code>
     * @return array
     */
    public static function getReportManagerSettings();

    /**
     * Method to return a label to display in ReportManager
     *
     * @return string Label
     */
    public static function getModelLabel();
}