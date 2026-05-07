<?php

return [
    'errors' => [
        'unauthorized' => 'Unauthorized.',
        'business_account_not_approved' => 'Business account is not approved.',
        'service_unavailable' => 'Service is not available.',
        'service_unavailable_for_requests' => 'Service is not available for requests.',
        'service_unavailable_for_chat' => 'Service is not available for chat.',
        'service_unavailable_for_reporting' => 'Service is not available for reporting.',
        'cannot_request_own_service' => 'Cannot request your own service.',
        'cannot_chat_with_self' => 'Cannot start conversation with yourself.',
        'duplicate_pending_request' => 'You already have a pending request for this service.',
        'only_pending_requests_can_be_cancelled' => 'Only pending requests can be cancelled.',
        'only_pending_requests_can_be_updated' => 'Only pending requests can be updated.',
        'review_requires_accepted_request' => 'Review is allowed only for accepted requests.',
        'request_already_reviewed' => 'This service request has already been reviewed.',
        'favorite_not_found' => 'Favorite not found.',
        'invalid_report_status' => 'Invalid report status.',
        'only_pending_services_can_be_approved' => 'Only pending services can be approved.',
        'only_pending_services_can_be_rejected' => 'Only pending services can be rejected.',
    ],

    'auth' => [
        'registered_otp_sent' => 'Registered successfully. OTP sent.',
        'login_otp_sent' => 'Credentials verified. OTP sent.',
        'login_successful' => 'Login successful.',
        'phone_verified' => 'Phone verified successfully.',
        'otp_resent' => 'OTP resent successfully.',
        'logged_out' => 'Logged out.',
        'profile_updated' => 'Profile updated successfully.',
    ],

    'business_accounts' => [
        'fetched' => 'Business accounts fetched successfully.',
        'single_fetched' => 'Business account fetched successfully.',
        'created' => 'Business account created successfully.',
        'updated' => 'Business account updated successfully.',
    ],

    'services' => [
        'fetched' => 'Services fetched successfully.',
        'single_fetched' => 'Service fetched successfully.',
        'created' => 'Service created successfully.',
        'updated' => 'Service updated successfully.',
        'deleted' => 'Service deleted successfully.',
    ],

    'sliders' => [
        'fetched' => 'Sliders fetched successfully.',
    ],

    'categories' => [
        'fetched' => 'Categories fetched successfully.',
    ],

    'subcategories' => [
        'fetched' => 'Subcategories fetched successfully.',
    ],

    'cities' => [
        'fetched' => 'Cities fetched successfully.',
    ],

    'activity_types' => [
        'fetched' => 'Activity types fetched successfully.',
    ],

    'dynamic_fields' => [
        'fetched' => 'Dynamic fields fetched successfully.',
    ],

    'content' => [
        'fetched' => 'Content page fetched successfully.',
        'privacy_policy' => [
            'title' => 'Privacy Policy',
            'body' => 'We collect the information needed to create accounts, verify business accounts, publish services, process service requests, support favorites, reports, notifications, and chat. We use this information only to operate and improve the platform, protect users, and comply with legal requirements.',
        ],
        'terms_of_use' => [
            'title' => 'Terms of Use',
            'body' => 'By using the platform, you agree to provide accurate information, use approved business accounts for commercial activity, respect other users, and avoid publishing misleading, illegal, or harmful content. Services and business accounts may be reviewed, approved, rejected, suspended, or removed by administration.',
        ],
    ],

    'favorites' => [
        'fetched' => 'Favorites fetched successfully.',
        'added' => 'Service added to favorites successfully.',
        'removed' => 'Service removed from favorites successfully.',
    ],

    'reports' => [
        'submitted' => 'Report submitted successfully.',
    ],

    'reviews' => [
        'fetched' => 'Reviews fetched successfully.',
        'created' => 'Review added successfully.',
    ],

    'notifications' => [
        'fetched' => 'Notifications fetched successfully.',
        'marked_read' => 'Notification marked as read.',
        'marked_all_read' => 'All notifications marked as read.',
    ],

    'notification_text' => [
        'business_account_pending_review_title' => 'New Business Account Pending Review',
        'business_account_pending_review_message' => 'A new business account is waiting for approval.',
        'business_account_approved_title' => 'Business Account Approved',
        'business_account_approved_message' => 'Your business account has been approved.',
        'business_account_rejected_title' => 'Business Account Rejected',
        'business_account_rejected_message' => 'Your business account has been rejected.',
        'service_pending_review_title' => 'New Service Pending Review',
        'service_pending_review_message' => 'A new service is waiting for approval.',
        'service_approved_title' => 'Service Approved',
        'service_approved_message' => 'Your service has been approved and published.',
        'service_rejected_title' => 'Service Rejected',
        'service_rejected_message' => 'Your service has been rejected by admin review.',
        'new_service_request_title' => 'New Service Request',
        'new_service_request_message' => 'You have received a new service request.',
    ],

    'chat' => [
        'conversations_fetched' => 'Conversations fetched successfully.',
        'conversation_created' => 'Conversation created successfully.',
        'messages_fetched' => 'Messages fetched successfully.',
        'message_sent' => 'Message sent successfully.',
        'messages_marked_read' => 'Messages marked as read.',
    ],

    'service_requests' => [
        'outgoing_fetched' => 'Outgoing requests fetched successfully.',
        'incoming_fetched' => 'Incoming requests fetched successfully.',
        'created' => 'Service request created successfully.',
        'accepted' => 'Service request accepted successfully.',
        'rejected' => 'Service request rejected successfully.',
        'cancelled' => 'Service request cancelled successfully.',
    ],
];
