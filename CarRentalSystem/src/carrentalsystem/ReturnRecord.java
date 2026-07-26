package carrentalsystem;

public class ReturnRecord {
    private String returnId, bookingId, returnDate, fuelLevel, damageDescription;
    private boolean hasDamage;
    private double damageFee, lateFee, totalPenalty;
    private int lateDays;

    public static final double LATE_FEE_PER_DAY = 50.0;

    public ReturnRecord() {}

    public ReturnRecord(String returnId, String bookingId, String returnDate, String fuelLevel,
                        boolean hasDamage, String damageDescription, double damageFee, int lateDays) {
        this.returnId = returnId; this.bookingId = bookingId; this.returnDate = returnDate;
        this.fuelLevel = fuelLevel; this.hasDamage = hasDamage; this.damageDescription = damageDescription;
        this.damageFee = damageFee; this.lateDays = lateDays;
        this.lateFee = lateDays * LATE_FEE_PER_DAY;
        this.totalPenalty = calculateTotalPenalty();
    }

    public String getReturnId() { return returnId; } public void setReturnId(String v) { returnId = v; }
    public String getBookingId() { return bookingId; } public void setBookingId(String v) { bookingId = v; }
    public String getReturnDate() { return returnDate; } public void setReturnDate(String v) { returnDate = v; }
    public String getFuelLevel() { return fuelLevel; } public void setFuelLevel(String v) { fuelLevel = v; }
    public boolean isHasDamage() { return hasDamage; } public void setHasDamage(boolean v) { hasDamage = v; }
    public String getDamageDescription() { return damageDescription; } public void setDamageDescription(String v) { damageDescription = v; }
    public double getDamageFee() { return damageFee; } public void setDamageFee(double v) { damageFee = v; }
    public double getLateFee() { return lateFee; } public void setLateFee(double v) { lateFee = v; }
    public int getLateDays() { return lateDays; }
    public void setLateDays(int v) { lateDays = v; lateFee = v * LATE_FEE_PER_DAY; }
    public double getTotalPenalty() { return totalPenalty; } public void setTotalPenalty(double v) { totalPenalty = v; }

    public double getFuelPenalty() {
        switch (fuelLevel) {
            case "Full": return 0; case "3/4": return 30; case "1/2": return 60;
            case "1/4": return 90; case "Empty": return 120; default: return 0;
        }
    }

    public double calculateTotalPenalty() {
        totalPenalty = lateFee + damageFee + getFuelPenalty();
        return totalPenalty;
    }

    @Override
    public String toString() {
        return String.format("%-6s %-6s %s  Fuel:%s  Damage:%s  Penalty:RM%.2f",
                returnId, bookingId, returnDate, fuelLevel,
                hasDamage ? damageDescription : "None", totalPenalty);
    }

    @Override public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof ReturnRecord)) return false;
        return this.returnId.equals(((ReturnRecord) obj).returnId);
    }
}
