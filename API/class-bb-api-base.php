<?php


class Bb_Api_Base
{
    public function sendSuccess($data, $meta = [], $code = 'data_fetched_succefully', $message = 'Data Fetched Successfully')
    {
        $data = [
            'success' => true,
            'code' => $code,
            'data' => $data,
            'message' => $message
        ];
        if(!empty($meta)) {
            $data['meta'] = $meta;
        }
        return $data;
    }

    public function sendError($code = 'data_fetched_faild', $message = 'Data Fetched Faild', $data = [])
    {
        return [
            'success' => false,
            'code' => $code,
            'data' => $data,
            'message' => $message
        ];
    }
}
