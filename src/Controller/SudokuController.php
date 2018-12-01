<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\SudokuBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SudokuController
{
    /**
     * @Route("/{values}", defaults={"values": null}, requirements={"values": "[0-9]*"}, name="sudoku_index")
     * @Template()
     *
     * @param string $values
     *
     * @return array
     */
    public function indexAction($values)
    {
        // Disable web profiler when using React
        if ($this->has('profiler')) {
            $this->get('profiler')->disable();
        }

        $sudoku = $this->createSudoku($values);

        return [
            'sudoku' => $sudoku,
        ];
    }

    /**
     * @Route("/state", name="sudoku_state")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function stateAction(Request $request)
    {
        $values = $request->query->get('values');

        $sudoku = $this->createSudoku($values);

        try {
            $sudoku->solve(false);
        } catch (Exception $exception) {
            // do nothing
        }

        return new JsonResponse(['sudoku' => $sudoku->toArray()]);
    }

    /**
     * @Route("/solve", name="sudoku_solve")
     * @Template()
     *
     * @param string $values
     *
     * @return JsonResponse
     */
    public function solveAction($values)
    {
        $sudoku = $this->createSudoku($values);
        $sudoku->solve(true);

        return new JsonResponse(['sudoku' => $sudoku->toArray()]);
    }

    /**
     * @param $values
     *
     * @return Puzzle
     */
    protected function createSudoku($values)
    {
        if (!is_string($values) || 81 != strlen($values)) {
            $values = str_repeat('0', 81);
        }

        $sudoku = new Puzzle($values);

        return $sudoku;
    }
}
