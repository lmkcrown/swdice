<!DOCTYPE html>
<html lang="sv">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/style.css">
        <title>Star Wars Dice</title>
    </head>
    <body>
    <h1>Star Wars Dice Roller</h1>
<?php

$diceRolled            = isset($_GET['ability']) ? true : false;

$dicePool['boost']      = isset($_GET['boost']) ? $_GET['boost'] : 0;

$dicePool['ability']    = isset($_GET['ability']) ? $_GET['ability'] : 1;

$dicePool['proficiency'] = isset($_GET['proficiency']) ? $_GET['proficiency'] : 0;

$dicePool['setback']    = isset($_GET['setback']) ? $_GET['setback'] : 0;

$dicePool['difficulty'] = isset($_GET['difficulty']) ? $_GET['difficulty'] : 1;

$dicePool['challenge']  = isset($_GET['challenge']) ? $_GET['challenge'] : 0;


function rollValues($dicePool)
{
    $swDiceFaces = array(   'boost' => 6,
                            'setback' => 6,
                            'ability' => 8,
                            'difficulty' => 8,
                            'proficiency' => 12,
                            'challenge' => 12);
    $rolledValues = array();

    foreach ($dicePool as $type => $nr) {
        if ($nr > 0) {
            for ($i = 1; $i <= $nr; $i++) {
                $rolledValues[$type][$i] = rand(1, $swDiceFaces[$type]);
            }
        } else {
            $rolledValues[$type][0] = 0; // set to 0 when no dice is rolled
        }
    }
    return $rolledValues;
}

function getSwSymbols($rolledValues)
{
    $swDiceSymbols = array(     'success' => 0,
                                'advantage' => 0,
                                'triumph' => 0,
                                'failure' => 0,
                                'threat' => 0,
                                'despair' => 0);

    foreach ($rolledValues as $type => $values) {
        switch ($type) {
            case 'boost':
                foreach ($values as $value) {
                    if ($value == 3) {
                        $swDiceSymbols['success'] += 1;
                    } else if ($value == 4) {
                        $swDiceSymbols['success'] += 1;
                        $swDiceSymbols['advantage'] += 1;
                    } else if ($value == 5) {
                        $swDiceSymbols['advantage'] += 2;
                    } else if ($value == 5) {
                        $swDiceSymbols['advantage'] += 1;
                    }
                }
                break;
            case 'setback':
                foreach ($values as $value) {
                    if ($value == 3 || $value == 4) {
                        $swDiceSymbols['failure'] += 1;
                    } else if ($value == 5 || $value == 6) {
                        $swDiceSymbols['threat'] += 1;
                    }
                }
                break;
            case 'ability':
                foreach ($values as $value) {
                    if ($value == 2 || $value == 3) {
                        $swDiceSymbols['success'] += 1;
                    } else if ($value == 4) {
                        $swDiceSymbols['success'] += 2;
                    } else if ($value == 5 || $value == 6) {
                        $swDiceSymbols['advantage'] += 1;
                    } else if ($value == 7) {
                        $swDiceSymbols['advantage'] += 1;
                        $swDiceSymbols['success'] += 1;
                    } else if ($value == 8) {
                        $swDiceSymbols['advantage'] += 2;
                    }
                }
                break;
            case 'difficulty':
                foreach ($values as $value) {
                    if ($value == 2) {
                        $swDiceSymbols['failure'] += 1;
                    } else if ($value == 3) {
                        $swDiceSymbols['failure'] += 2;
                    } else if ($value == 4 || $value == 5 || $value == 6) {
                        $swDiceSymbols['threat'] += 1;
                    } else if ($value == 7) {
                        $swDiceSymbols['threat'] += 2;
                    } else if ($value == 8) {
                        $swDiceSymbols['threat'] += 1;
                        $swDiceSymbols['failure'] += 1;
                    }
                }
                break;
            case 'proficiency':
                foreach ($values as $value) {
                    if ($value == 2 || $value == 3) {
                        $swDiceSymbols['success'] += 1;
                    } else if ($value == 4 || $value == 5) {
                        $swDiceSymbols['success'] += 2;
                    } else if ($value == 6) {
                        $swDiceSymbols['advantage'] += 1;
                    } else if ($value == 7 || $value == 8 || $value == 9) {
                        $swDiceSymbols['advantage'] += 1;
                        $swDiceSymbols['success'] += 1;
                    } else if ($value == 10 || $value == 11) {
                        $swDiceSymbols['advantage'] += 2;
                    } else if ($value == 12) {
                        $swDiceSymbols['triumph'] += 1;
                    }
                }
                break;
            case 'challenge':
                foreach ($values as $value) {
                    if ($value == 2 || $value == 3) {
                        $swDiceSymbols['failure'] += 1;
                    } else if ($value == 4 || $value == 5) {
                        $swDiceSymbols['failure'] += 2;
                    } else if ($value == 6 || $value == 7) {
                        $swDiceSymbols['threat'] += 1;
                    } else if ($value == 8 || $value == 9) {
                        $swDiceSymbols['threat'] += 1;
                        $swDiceSymbols['failure'] += 1;
                    } else if ($value == 10 || $value == 11) {
                        $swDiceSymbols['threat'] += 2;
                    } else if ($value == 12) {
                        $swDiceSymbols['despair'] += 1;
                    }
                }
                break;
        }
    }
    return $swDiceSymbols;

}

function resultPool($swSymbols)
{
    $success = $swSymbols['success'] + $swSymbols['triumph'];
    $failure = $swSymbols['failure'] + $swSymbols['despair'];

    $result['success'] = $success - $failure;
    $result['advantage'] = $swSymbols['advantage'] - $swSymbols['threat'];
    $result['triumph'] = $swSymbols['triumph'];
    $result['despair'] = $swSymbols['despair'];

    return $result;
}

function displayResult($result)
{
    $file ="";

    echo '<h4>Result: </h4><p class="symbols">';

    foreach ($result as $k => $v) {
        switch ($k) {
            case 'success':
                if ($v > 0) {
                    $file = 'img/success.png';
                } else {
                    $file = 'img/failure.png';
                }
                break;

            case 'advantage':
                if ($v > 0) {
                    $file = 'img/advantage.png';
                } else {
                    $file = 'img/threat.png';
                }
                break;

            case 'triumph':
                $file = 'img/triumph.png';
                break;

            case 'despair':
                $file = 'img/despair.png';
                break;
        }
        $alt = basename($file);
        for ($i = 0; $i < abs($v); $i++) {
            echo "<img src=\"$file\" alt=\"$alt\" />";
        }
        echo "\t";
    }
    echo '</p><p>Note that triumph and despair have already been added to success / failures.</p>';

}

function displayPool($dicePool)
{
    echo '<h4>Dicepool: </h4><p><table><tr>';
    foreach ($dicePool as $type => $nr) {
        if ($nr > 0) {
            for ($i=1; $i <= $nr; $i++) {
                echo "<td><figure class=\"$type\" />";
            }
        }
    }
    echo "</tr></table></p>";
}



// if any dice rolled show result
if ($diceRolled) {

    // display dice rolled
    displayPool($dicePool);

    // create array with rolled values
    $diceRolls = rollValues($dicePool);
    // echo '<p>';
    // foreach ($test as $k => $v) {
    //     echo $k . ": ";
    //     foreach ($v as $value) {
    //         echo $value . ", ";
    //     }
    //     echo "<br>";
    // }

    // create array with sw dice symbols
    $symbols = getSwSymbols($diceRolls);
    // echo '<p>';
    // foreach ($testSymb as $k => $v) {
    //     echo $k . ": " . $v . "<br>";
    // }

    // show symbols of dice pool
    $result = resultPool($symbols);
    // echo '<p>';
    // foreach ($result as $k => $v) {
    //     echo $k . ": " . $v . "<br>";
    // }

    // show result
    displayResult($result);
}


 ?>

    <form>
        <table>
            <tr>
                <td>Boost
                <td><figure class="boost" />
                <td><input type="number" name="boost" value=<?=$dicePool['boost']?> min="0" max="6">
            </tr>
            <tr>
                <td>Ability
                <td><figure class="ability" />
                <td><input type="number" name="ability" value=<?=$dicePool['ability']?> min="0" max="6">
            </tr>
            <tr>
                <td>Proficiency
                <td><figure class="proficiency" />
                <td><input type="number" name="proficiency" value=<?=$dicePool['proficiency']?> min="0" max="6">
            </tr>
            <tr>
                <td>Setback
                <td><figure class="setback" />
                <td><input type="number" name="setback" value=<?=$dicePool['setback']?> min="0" max="6">
            </tr>
            <tr>
                <td>Difficulty
                <td><figure class="difficulty" />
                <td><input type="number" name="difficulty" value=<?=$dicePool['difficulty']?> min="0" max="6">
            </tr>
            <tr>
                <td>Challenge
                <td><figure class="challenge" />
                <td><input type="number" name="challenge" value=<?=$dicePool['challenge']?> min="0" max="6">
            </tr>
            <tr>
                <td><a href="?">Clear</a><br> <td><input type="submit" value="Roll">
            </tr>
        </table>
    </form>

    </body>
</html>
