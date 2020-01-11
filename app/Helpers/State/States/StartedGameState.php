<?php

namespace App\Helpers\State\States;

use App\Helpers\Cards\Cards;
use App\Helpers\State\State;

class StartedGameState extends State
{
    private $cards;
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->buttons    = ['changeCards', 'notChange'];
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.startedMessage');
        $this->context->indicator  = 'ready';
        $this->context->money      = $this->context->extractMoney();
        $this->context->bankMessages = $this->context->extractBankMessages();
        $this->context->userCards  = $this->context->extractUserCardsFromRedis();

        $keyStorage  = $this->context->getKeyStorageForCards();
        $this->cards = new Cards($keyStorage);
    }

    public function waitingOpponentUser()
    {
    }

    public function connectionOpponentUser()
    {
    }

    public function connectionCurrentUser()
    {
    }

    public function startGame()
    {
    }
    
    public function addMoney()
    {
    }

    public function check()
    {
    }
    
    public function equalAndAdd()
    {
    }

    public function equal()
    {
    }

    public function gameOver()
    {
    }

    public function changeCards()
    {
        $indexes    = $this->context->request->cardsIndexForChange;
        $cntIndexes = 0;

        $indexesArr = explode(",", $indexes);
        $cntIndexes = count($indexesArr);

        if ($this->context->role === 'currentUser') {
            // если нет команды "не меняю"
            if ($indexes !== false) {
                $this->updateUserCards(10, $cntIndexes, $indexesArr);
            }

            $this->context->saveCountFirstUserChangeCards($cntIndexes);
            $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage3',
                ['user' => $this->context->opponentUser->name]);
            $buttons = 'addMoney,noMoney';
            // $this->context->indicator = 'ready';
            $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);

            \App\Events\SendBettingStatus::dispatch();
        }

        if ($this->context->role === 'opponentUser') {
            $countCards = (int) $this->context->getCountFirstUserChangeCards();

            // если нет команды "не меняю"
            if ($indexes !== false) {
                $this->updateUserCards(10 + $countCards, $cntIndexes, $indexesArr);
            }

            \App\Events\SendFinishChangeStatus::dispatch();
            $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage4',
                ['user' => $this->context->opponentUser->name]);
            $buttons = 'equal,equalAndAdd,gameOver';
            $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);
        }
    }

    private function updateUserCards($startIndex, $cntIndexes, $indexesArr)
    {
        $newCards = $this->cards->getCards($startIndex, $cntIndexes);
        for ($i = 0, $j = 0; $i < 5; $i++) {
            if ($j < $cntIndexes && $i == (int) $indexesArr[$j]) {
                $this->context->userCards[$i] = $newCards[$j];
                ++$j;
            }
        }
        $this->context->saveUserCards();
    }
}
