<?php
namespace App\Common;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\Database\Exception\MissingConnectionException;
use Exception;

/**
 * データ機能クラス
 */
class SQLiteFunction
{

    private $conn = null;

    /**
     * 
     */
    public function __construct()
    {
        try
        {
            $this->conn = ConnectionManager::get('SQLite');
        }
        catch (Exception $ex)
        {
            Log::debug(__METHOD__, "debug");
            Log::debug($ex, "debug");
        }
    }

    /**
     * Check connect
     */
    private function isConnect()
    {
        try
        {
            if ($this->conn->connect())
            {
                return true;
            }
        }
        catch (MissingConnectionException $ex)
        {
            Log::debug(__METHOD__, "debug");
            Log::debug($ex, "debug");
        }
        catch (Exception $ex)
        {
            Log::debug(__METHOD__, "debug");
            Log::debug($ex, "debug");
        }
        return false;
    }

    /**
     * エラーコードの取得
     * 
     * $unitID int ユニットID
     * $errorCode string エラーコード
     * 
     * return array|false
     */
    public function GetErrorInfo($unitID, $errorCode)
    {
        try
        {
            if ($this->isConnect())
            {
                $sql = "
                    SELECT * 
                    FROM M_ErrorCode
                    WHERE UnitID = :UnitID AND ErrorCode = :ErrorCode";
                $statement = $this->conn->prepare($sql);
                $statement->bindValue('UnitID', $unitID, 'integer');
                $statement->bindValue('ErrorCode', $errorCode, 'string');
                $statement->execute();
                $result = $statement->fetch('assoc');
                if ($result)
                {
                    return  $result;
                }
            }
        }
        catch(Exception $ex)
        {
            Log::debug(__METHOD__, "debug");
            Log::debug($ex, "debug");
        }
        return false;
    }
}
