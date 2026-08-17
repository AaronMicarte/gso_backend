-- Add partial delivery receipt support
ALTER TABLE `deliveryreceipt`
  ADD COLUMN `IsPartial` tinyint(1) NOT NULL DEFAULT 0 AFTER `InspectionCompletedAt`,
  ADD COLUMN `FinalizedAt` timestamp NULL DEFAULT NULL AFTER `IsPartial`;
