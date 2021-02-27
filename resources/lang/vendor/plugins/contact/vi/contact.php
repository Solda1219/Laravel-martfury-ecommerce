<?php

return [
    'menu'                 => 'Liên hệ',
    'model'                => 'Liên hệ',
    'models'               => 'Liên hệ',
    'list'                 => 'Danh sách liên hệ',
    'edit'                 => 'Xem liên hệ',
    'tables'               =>
        [
            'phone'     => 'Điện thoại',
            'email'     => 'Email',
            'full_name' => 'Họ tên',
            'time'      => 'Thời gian',
            'address'   => 'Địa địa chỉ',
            'subject'   => 'Tiêu đề',
            'content'   => 'Nội dung',
        ],
    'form'                 =>
        [
            'is_read' => 'Đã xem?',
            'status'  => 'Trạng thái',
        ],
    'notices'              =>
        [
            'no_select'              => 'Chọn ít nhất 1 liên hệ để thực hiện hành động này!',
            'update_success_message' => 'Cập nhật thành công',
        ],
    'cannot_delete'        => 'Không thể xóa liên hệ này',
    'deleted'              => 'Liên hệ đã được xóa',
    'contact_information'  => 'Thông tin liên hệ',
    'email'                =>
        [
            'title'    => 'Thông tin liên hệ mới',
            'success'  => 'Gửi tin nhắn thành công!',
            'failed'   => 'Có lỗi trong quá trình gửi tin nhắn!',
            'required' => 'Email không được để trống',
            'email'    => 'Địa chỉ email không hợp lệ',
            'header'   => 'Email',
        ],
    'name'                 =>
        [
            'required' => 'Họ tên không được để trống',
        ],
    'content'              =>
        [
            'required' => 'Nội dung tin nhắn không được để trống',
        ],
    'g-recaptcha-response' =>
        [
            'required' => 'Hãy xác minh không phải là robot trước khi gửi tin nhắn.',
            'captcha'  => 'Bạn chưa xác minh không phải là robot thành công.',
        ],
    'confirm_not_robot'    => 'Xác nhận không phải người máy',
    'contact_sent_from'    => 'Liên hệ này được gửi từ',
    'form_address'         => 'Địa chỉ',
    'form_email'           => 'Thư điện tử',
    'form_message'         => 'Thông điệp',
    'form_name'            => 'Họ tên',
    'form_phone'           => 'Số điện thoại',
    'message_content'      => 'Nội dung thông điệp',
    'required_field'       => 'Những trường có dấu (<span style="color: red">*</span>) là bắt buộc.',
    'send_btn'             => 'Gửi tin nhắn',
    'sender'               => 'Người gửi',
    'sender_address'       => 'Địa chỉ',
    'sender_email'         => 'Thư điện tử',
    'sender_phone'         => 'Số điện thoại',
    'sent_from'            => 'Thư được gửi từ',
    'mark_as_read'         => 'Đánh dấu đã đọc',
    'mark_as_unread'       => 'Đánh dấu chưa đọc',
    'address'              => 'Địa chỉ',
    'message'              => 'Liên hệ',
    'new_msg_notice'       => 'Bạn có <span class="bold">:count</span> tin nhắn mới',
    'phone'                => 'Điện thoại',
    'statuses'             => [
        'read'   => 'Đã đọc',
        'unread' => 'Chưa đọc',
    ],
    'view_all'             => 'Xem tất cả',
    'settings'             => [
        'email' => [
            'title'       => 'Liên hệ',
            'description' => 'Cấu hình thông tin cho mục liên hệ',
            'templates'   => [
                'notice_title'       => 'Thông báo tới admin',
                'notice_description' => 'Mẫu nội dung email gửi tới admin khi có liên hệ mới',
            ],
        ],
    ],
];
