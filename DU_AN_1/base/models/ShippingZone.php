<?php

class ShippingZone extends BaseModel
{
    protected $table = 'shipping_zones';

    public function getByLocation($province, $district = null)
    {
        if ($district) {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE province = :province AND district = :district 
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':province' => $province, ':district' => $district]);
            $result = $stmt->fetch();
            if ($result) return $result;
        }

        $sql = "SELECT * FROM {$this->table} 
                WHERE province = :province AND district IS NULL 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':province' => $province]);
        return $stmt->fetch();
    }

    public function getDefaultFee()
    {
        $sql = "SELECT base_fee FROM {$this->table} LIMIT 1";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result ? $result['base_fee'] : 15000;
    }
}
