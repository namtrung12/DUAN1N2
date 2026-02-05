<?php

class Wallet extends BaseModel
{
    protected string $collection = 'wallets';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getByUserId(int $userId): array
    {
        $wallets = $this->all();
        foreach ($wallets as $wallet) {
            if ((int)($wallet['user_id'] ?? 0) === $userId) {
                return $wallet;
            }
        }
        $wallet = [
            'id' => $this->store->nextId($this->collection),
            'user_id' => $userId,
            'balance' => 0
        ];
        $wallets[] = $wallet;
        $this->saveAll($wallets);
        return $wallet;
    }

    public function getTransactions(int $userId): array
    {
        $transactions = $this->store->read('wallet_transactions');
        $result = [];
        foreach ($transactions as $transaction) {
            if ((int)($transaction['user_id'] ?? 0) === $userId) {
                $result[] = $transaction;
            }
        }
        usort($result, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
        return $result;
    }

    public function deposit(int $userId, int $amount, string $description = 'Nạp tiền ví'): void
    {
        $this->adjustBalance($userId, $amount, 'deposit', $description);
    }

    public function debit(int $userId, int $amount, string $description = 'Thanh toán'): bool
    {
        $wallet = $this->getByUserId($userId);
        if ((int)$wallet['balance'] < $amount) {
            return false;
        }
        $this->adjustBalance($userId, -$amount, 'payment', $description);
        return true;
    }

    public function refund(int $userId, int $amount, string $description = 'Hoàn tiền'): void
    {
        $this->adjustBalance($userId, $amount, 'refund', $description);
    }

    private function adjustBalance(int $userId, int $amount, string $type, string $description): void
    {
        $wallets = $this->all();
        $found = false;
        foreach ($wallets as $index => $wallet) {
            if ((int)($wallet['user_id'] ?? 0) === $userId) {
                $wallet['balance'] = max(0, (int)($wallet['balance'] ?? 0) + $amount);
                $wallets[$index] = $wallet;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $wallets[] = [
                'id' => $this->store->nextId($this->collection),
                'user_id' => $userId,
                'balance' => max(0, $amount)
            ];
        }
        $this->saveAll($wallets);

        $transactions = $this->store->read('wallet_transactions');
        $transactions[] = [
            'id' => $this->store->nextId('wallet_transactions'),
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->store->write('wallet_transactions', $transactions);
    }
}
