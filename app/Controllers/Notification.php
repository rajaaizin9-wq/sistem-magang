<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notification extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Buka notifikasi: tandai sudah dibaca dan arahkan ke link tujuan
     */
    public function open($id)
    {
        $userId = session()->get('userId');
        $notif  = $this->notificationModel->where('id', $id)->where('user_id', $userId)->first();

        if ($notif) {
            if (empty($notif['read_at'])) {
                $this->notificationModel->update($id, [
                    'read_at' => date('Y-m-d H:i:s'),
                ]);
            }

            if (!empty($notif['link'])) {
                return redirect()->to(base_url($notif['link']));
            }
        }

        return redirect()->back();
    }

    /**
     * Tandai seluruh notifikasi user saat ini sebagai sudah dibaca
     */
    public function readAll()
    {
        $userId = session()->get('userId');
        $this->notificationModel->where('user_id', $userId)
                                 ->where('read_at IS NULL')
                                 ->set(['read_at' => date('Y-m-d H:i:s')])
                                 ->update();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}

