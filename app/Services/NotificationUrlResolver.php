<?php

namespace App\Services;

use App\Models\LandlordApplication;
use App\Models\Notification;
use App\Models\Property;
use App\Models\Transaction;
use App\Models\User;

class NotificationUrlResolver
{
    public function resolve(Notification $notification, ?User $viewer = null): ?string
    {
        $viewer = $viewer ?? $notification->user;
        if (!$viewer) {
            return null;
        }

        $notifiable = $notification->notifiable;

        return match ($notification->type) {
            'like', 'review' => $this->propertyUrl($notifiable, $viewer, 'published-section'),
            'approve' => $this->approveUrl($notifiable, $viewer),
            'reject' => $this->rejectUrl($notifiable, $viewer),
            'pending' => $this->pendingUrl($notifiable, $viewer),
            'transaction' => $this->transactionUrl($notifiable, $viewer),
            default => $this->dashboardUrl($viewer),
        };
    }

    private function propertyUrl($notifiable, User $viewer, string $landlordSection = 'published-section'): ?string
    {
        if (!$notifiable instanceof Property) {
            return $this->dashboardUrl($viewer);
        }

        if ($notifiable->status === 'approved') {
            return route('properties.show', $notifiable);
        }

        if ($viewer->id === $notifiable->user_id || $viewer->isLandlord()) {
            $section = $notifiable->status === 'rejected' ? 'rejected-section' : 'pending-section';

            return route('landlord.dashboard') . '#' . $section;
        }

        if ($viewer->role === 'admin') {
            return route('admin.dashboard') . '?highlight_property=' . $notifiable->id . '#properties-section';
        }

        return route('landlord.dashboard') . '#' . $landlordSection;
    }

    private function approveUrl($notifiable, User $viewer): ?string
    {
        if ($notifiable instanceof LandlordApplication) {
            return route('landlord.dashboard');
        }

        if ($notifiable instanceof Property) {
            return route('landlord.dashboard') . '#published-section';
        }

        if ($notifiable instanceof Transaction) {
            return $this->transactionUrl($notifiable, $viewer);
        }

        return $this->dashboardUrl($viewer);
    }

    private function rejectUrl($notifiable, User $viewer): ?string
    {
        if ($notifiable instanceof LandlordApplication) {
            return route('landlord.apply');
        }

        if ($notifiable instanceof Property) {
            return route('landlord.dashboard') . '#rejected-section';
        }

        if ($notifiable instanceof Transaction) {
            return $this->transactionUrl($notifiable, $viewer);
        }

        return $this->dashboardUrl($viewer);
    }

    private function pendingUrl($notifiable, User $viewer): ?string
    {
        if ($notifiable instanceof LandlordApplication) {
            if ($viewer->role === 'admin') {
                return route('admin.dashboard') . '?highlight_application=' . $notifiable->id . '#landlords-section';
            }

            return route('landlord.dashboard');
        }

        if ($notifiable instanceof Property) {
            if ($viewer->role === 'admin') {
                return route('admin.dashboard') . '#properties-section';
            }

            return route('landlord.dashboard') . '#pending-section';
        }

        if ($notifiable instanceof Transaction) {
            return $this->transactionUrl($notifiable, $viewer);
        }

        return $this->dashboardUrl($viewer);
    }

    private function transactionUrl($notifiable, User $viewer): ?string
    {
        if (!$notifiable instanceof Transaction) {
            return $this->dashboardUrl($viewer);
        }

        if ($viewer->role === 'admin') {
            return route('admin.dashboard') . '#transactions-section';
        }

        if ((int) $viewer->id === (int) $notifiable->user_id) {
            return route('transactions.show', $notifiable);
        }

        $notifiable->loadMissing('property');
        if ($notifiable->property && (int) $viewer->id === (int) $notifiable->property->user_id) {
            $section = in_array($notifiable->status, ['confirmed', 'completed'], true) || $notifiable->paid
                ? 'active-section'
                : 'requests-section';

            return route('landlord.dashboard') . '?open_request=' . $notifiable->id . '#' . $section;
        }

        return route('profile.transactions');
    }

    private function dashboardUrl(User $viewer): ?string
    {
        if ($viewer->role === 'admin') {
            return route('admin.dashboard');
        }

        if ($viewer->isLandlord()) {
            return route('landlord.dashboard');
        }

        return route('profile.transactions');
    }
}
