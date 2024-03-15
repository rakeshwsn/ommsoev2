<?php

declare(strict_types=1);

namespace App\Traits;

use Admin\Component\Models\ComponentModel;

/**
 * Trait TreeTrait
 * @package App\Traits
 */
trait TreeTrait
{
    /**
     * @param array $filter
     * @return string
     */
    public function getTree(array $filter = []): string
    {
        $componentModel = new ComponentModel();
        $comps = $componentModel->getAll($filter);
        $tree = $this->buildTree($comps);

        return $this->nestedHTMLTree($tree);
    }

    /**
     * @return array
     */
    public function getTreeArray(): array
    {
        $componentModel = new ComponentModel();
        $comps = $componentModel->getAll();

        return $this->treeBuildHelper($comps);
    }

    /**
     * @param array $flatList
     * @param string $parentCol
     * @param string $idCol
     * @return array
     */
    private function buildTree(array $flatList, string $parentCol = 'parent', string $idCol = 'id'): array
    {
        if (empty($flatList)) {
            return [];
        }

        $grouped = [];
        foreach ($flatList as $node) {
            $grouped[$node[$parentCol]][] = $node;
        }

        $fnBuilder = function ($siblings) use (&$fnBuilder, $grouped, $idCol) {
            foreach ($siblings as $k => $sibling) {
                $id = $sibling[$idCol];
                if (isset($grouped[$id])) {
                    $sibling['children'] = $fnBuilder($grouped[$id]);
                }
                $siblings[$k] = $sibling;
            }

            return $siblings;
        };

        return $fnBuilder($grouped[0]);
    }

    /**
     * @return array
     */
    public function treeBuildHelper(array $flatList): array
    {
        return $this->buildTree($flatList);
    }

    /**
     * @param array $components
     * @return array
     */
    private function calculateAbstractSum(array $components): array
    {
        foreach ($components as &$component) {
            if (isset($component['children']) && !empty($component['children'])) {
                $this->calculateAbstractSum($component['children']);

                $component['ob_phy'] += $component['children'][0]['ob_phy'];
                $component['ob_fin'] += $component['children'][0]['ob_fin'];
                $component['bud_phy'] += $component['children'][0]['bud_phy'];
                $component['bud_fin'] += $component['children'][0]['bud_fin'];
                $component['fr_upto_phy'] += $component['children'][0]['fr_upto_phy'];
                $component['fr_upto_fin'] += $component['children'][0]['fr_upto_fin'];
                $component['fr_mon_phy'] += $component['children'][0]['fr_mon_phy'];
                $component['fr_mon_fin'] += $component['children'][0]['fr_mon_fin'];
                $component['fr_cum_phy'] += $component['children'][0]['fr_cum_phy'];
                $component['fr_cum_fin'] += $component['children'][0]['fr_cum_fin'];
                $component['exp_upto_phy'] += $component['children'][0]['exp_upto_phy'];
                $component['exp_upto_fin'] += $component['children'][0]['exp_upto_fin'];
                $component['exp_mon_phy'] += $component['children'][0]['exp_mon_phy'];
                $component['exp_mon_fin'] += $component['children'][0]['exp_mon_fin'];
                $component['exp_cum_phy'] += $component['children'][0]['exp_cum_phy'];
                $component['exp_cum_fin'] += $component['children'][0]['exp_cum_fin'];
           
