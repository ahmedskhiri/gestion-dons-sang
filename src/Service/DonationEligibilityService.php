<?php
// src/Service/DonationEligibilityService.php

namespace App\Service;

use App\Entity\Donateur;
use DateTime;
use DateInterval;

class DonationEligibilityService
{
    /**
     * Calcule la date de la prochaine date éligible pour un don (56 jours).
     */
    public function calculateNextEligibleDate(Donateur $donateur): array
    {
        // On utilise le getter correct du Donateur
        $lastDonationDate = $donateur->getDernierDateDon(); 

        // Si jamais donné, éligible immédiatement
        if ($lastDonationDate === null) {
            return [
                'eligible' => true,
                'nextDate' => null,
                'message' => "Vous êtes éligible pour un don. Merci de prendre un rendez-vous !"
            ];
        }

        // Calcul de la prochaine date éligible (+56 jours)
        $nextEligibleDate = clone $lastDonationDate;
        $nextEligibleDate->add(new DateInterval('P56D')); 

        $today = new DateTime();

        // Vérification
        if ($today >= $nextEligibleDate) {
            return [
                'eligible' => true,
                'nextDate' => null,
                'message' => "Vous êtes éligible pour un don. Merci de prendre un rendez-vous !"
            ];
        } else {
            $diff = $today->diff($nextEligibleDate);
            $daysLeft = $diff->days;
            
            return [
                'eligible' => false,
                'nextDate' => $nextEligibleDate,
                'message' => "Vous ne serez éligible qu'à partir du " . $nextEligibleDate->format('d/m/Y') . " (encore $daysLeft jours)."
            ];
        }
    }
}