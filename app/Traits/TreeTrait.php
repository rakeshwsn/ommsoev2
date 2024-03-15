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
        $compModel = new ComponentModel();
        $comps = $compModel->getAll($filter);
        $tree = $this->buildTree($comps);

        return $this->nestedHTMLTree($tree);
    }

    /**
     * @return array
     */
    public function getTreeArray(): array
    {
        $compModel = new ComponentModel();
        $comps = $compModel->getAll();

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
                if (isset($grouped[$k])) {
                    $sibling['children'] = $fnBuilder($grouped[$k]);
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

                $sum = [
                    'ob_phy' => 0,
                    'ob_fin' => 0,
                    'bud_phy' => 0,
                    'bud_fin' => 0,
                    'fr_upto_phy' => 0,
                    'fr_upto_fin' => 0,
                    'fr_mon_phy' => 0,
                    'fr_mon_fin' => 0,
                    'fr_cum_phy' => 0,
                    'fr_cum_fin' => 0,
                    'exp_upto_phy' => 0,
                    'exp_upto_fin' => 0,
                    'exp_mon_phy' => 
