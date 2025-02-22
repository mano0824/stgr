<?php
/**
 * 決済サービスタイプ：楽天、コマンド名：与信の要求Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class RakutenAuthorizeRequestDto extends AbstractPaymentRequestDto
{

    /**
     * 決済サービスタイプ<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
    private $SERVICE_TYPE = "rakuten";

    /**
     * 決済サービスコマンド<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
    private $SERVICE_COMMAND = "Authorize";

    /**
     * 取引ID<br>
     * 半角英数字<br/>
     * 最大桁数：100<br/>
     * - マーチャント側で取引を一意に表す注文管理IDを指定します。<br/>
     * - 申込処理ごとに一意である必要があります。<br/>
     * - 半角英数字、“-”（ハイフン）、“_”（アンダースコア）も使用可能です。<br/>
     */
    private $orderId;

    /**
     * 決済金額<br>
     * 半角数字<br/>
     * 最大桁数：8<br/>
     * 決済金額を指定します。<br/>
     * - 100 以上かつ 99999999 以下<br/>
     * <br/>
     */
    private $amount;

    /**
     * 与信同時売上フラグ<br>
     * 英字（boolean）<br/>
     * - true : 与信同時売上<br/>
     * - false: 与信のみ<br/>
     * ※ 未指定の場合は、false:与信のみ。<br/>
     * ※ マーチャント登録時に商品タイプを「物販」で登録している場合、与信同時売上は使用できません。<br/>
     */
    private $withCapture;

    /**
     * 商品番号<br>
     * 半角英数字<br/>
     * 最大桁数：64<br/>
     * マーチャントシステム内で商品やサービスを特定するID<br/>
     */
    private $itemId;

    /**
     * 商品名<br>
     * 文字列<br/>
     * 最大桁数：255<br/>
     * 商品名<br/>
     * - 最大文字数：255文字<br/>
     */
    private $itemName;

    /**
     * 決済完了時URL<br>
     * URL(半角)<br/>
     * 最大桁数：256<br/>
     * 決済完了後に、店舗側サイトに画面遷移を戻すためのURLを指定します。<br/>
     * - 半角256文字以内<br/>
     * <br/>
     * ※ 未指定の場合は、マーチャント登録時に設定した値を使用<br/>
     */
    private $successUrl;

    /**
     * 決済エラー時URL<br>
     * URL(半角)<br/>
     * 最大桁数：256<br/>
     * 決済キャンセルエラー時に、店舗側サイトに画面遷移を戻すためのURLを指定します。<br/>
     * - 半角256文字以内<br/>
     * <br/>
     * ※ 未指定の場合は、マーチャント登録時に設定した値を使用<br/>
     */
    private $errorUrl;

    /**
     * プッシュ先URL<br>
     * URL(半角)<br/>
     * 最大桁数：256<br/>
     * 「ダミー取引」時のプッシュURLを指定します。<br/>
     * - 半角256文字以内<br/>
     * <br/>
     * ※ 本パラメータは店舗側システムの開発時にのみ利用されることを想定しており、ダミー取引で指定可能です。<br/>
     */
    private $pushUrl;


    /**
     * ログ用文字列(マスク済み)<br>
     * 半角英数字<br>
     */
    private $maskedLog;


    /**
     * 決済サービスタイプを取得する<br>
     * @return 決済サービスタイプ<br>
     */
    public function getServiceType() {
        return $this->SERVICE_TYPE;
    }

    /**
     * 決済サービスコマンドを取得する<br>
     * @return 決済サービスコマンド<br>
     */
    public function getServiceCommand() {
        return $this->SERVICE_COMMAND;
    }

    /**
     * 取引IDを取得する<br>
     * @return 取引ID<br>
     */
    public function getOrderId() {
        return $this->orderId;
    }

    /**
     * 取引IDを設定する<br>
     * @param orderId 取引ID<br>
     */
    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    /**
     * 決済金額を取得する<br>
     * @return 決済金額<br>
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * 決済金額を設定する<br>
     * @param amount 決済金額<br>
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * 与信同時売上フラグを取得する<br>
     * @return 与信同時売上フラグ<br>
     */
    public function getWithCapture() {
        return $this->withCapture;
    }

    /**
     * 与信同時売上フラグを設定する<br>
     * @param withCapture 与信同時売上フラグ<br>
     */
    public function setWithCapture($withCapture) {
        $this->withCapture = $withCapture;
    }

    /**
     * 商品番号を取得する<br>
     * @return 商品番号<br>
     */
    public function getItemId() {
        return $this->itemId;
    }

    /**
     * 商品番号を設定する<br>
     * @param itemId 商品番号<br>
     */
    public function setItemId($itemId) {
        $this->itemId = $itemId;
    }

    /**
     * 商品名を取得する<br>
     * @return 商品名<br>
     */
    public function getItemName() {
        return $this->itemName;
    }

    /**
     * 商品名を設定する<br>
     * @param itemName 商品名<br>
     */
    public function setItemName($itemName) {
        $this->itemName = $itemName;
    }

    /**
     * 決済完了時URLを取得する<br>
     * @return 決済完了時URL<br>
     */
    public function getSuccessUrl() {
        return $this->successUrl;
    }

    /**
     * 決済完了時URLを設定する<br>
     * @param successUrl 決済完了時URL<br>
     */
    public function setSuccessUrl($successUrl) {
        $this->successUrl = $successUrl;
    }

    /**
     * 決済エラー時URLを取得する<br>
     * @return 決済エラー時URL<br>
     */
    public function getErrorUrl() {
        return $this->errorUrl;
    }

    /**
     * 決済エラー時URLを設定する<br>
     * @param errorUrl 決済エラー時URL<br>
     */
    public function setErrorUrl($errorUrl) {
        $this->errorUrl = $errorUrl;
    }

    /**
     * プッシュ先URLを取得する<br>
     * @return プッシュ先URL<br>
     */
    public function getPushUrl() {
        return $this->pushUrl;
    }

    /**
     * プッシュ先URLを設定する<br>
     * @param pushUrl プッシュ先URL<br>
     */
    public function setPushUrl($pushUrl) {
        $this->pushUrl = $pushUrl;
    }


    /**
     * ログ用文字列(マスク済み)を設定する<br>
     * @param  maskedLog ログ用文字列(マスク済み)<br>
     */
    public function _setMaskedLog($maskedLog) {
        $this->maskedLog = $maskedLog;
    }

    /**
     * ログ用文字列(マスク済み)を取得する<br>
     * @return ログ用文字列(マスク済み)<br>
     */
    public function __toString() {
        return (string)$this->maskedLog;
    }

}
?>