package carrentalsystem;

public class Extras {
    private boolean gpsRental, insurancePackage, childSeat;
    private String discountCode;
    private double discountPercent;

    public static final double GPS_RATE = 25.0;
    public static final double INSURANCE_RATE = 40.0;
    public static final double CHILD_SEAT_RATE = 15.0;

    public Extras() { this.discountCode = ""; this.discountPercent = 0.0; }

    public Extras(boolean gps, boolean insurance, boolean childSeat, String code, double percent) {
        this.gpsRental = gps; this.insurancePackage = insurance; this.childSeat = childSeat;
        this.discountCode = code; this.discountPercent = percent;
    }

    public boolean isGpsRental() { return gpsRental; } public void setGpsRental(boolean v) { gpsRental = v; }
    public boolean isInsurancePackage() { return insurancePackage; } public void setInsurancePackage(boolean v) { insurancePackage = v; }
    public boolean isChildSeat() { return childSeat; } public void setChildSeat(boolean v) { childSeat = v; }
    public String getDiscountCode() { return discountCode; } public void setDiscountCode(String v) { discountCode = v; }
    public double getDiscountPercent() { return discountPercent; } public void setDiscountPercent(double v) { discountPercent = v; }

    public double calculateExtrasCost(int days) {
        double t = 0;
        if (gpsRental) t += GPS_RATE * days;
        if (insurancePackage) t += INSURANCE_RATE * days;
        if (childSeat) t += CHILD_SEAT_RATE * days;
        return t;
    }

    public double calculateExtrasCost(int days, boolean applyDiscount) {
        double t = calculateExtrasCost(days);
        if (applyDiscount && discountPercent > 0) t -= t * (discountPercent / 100.0);
        return t;
    }

    public static double validateDiscount(String code) {
        switch (code.toUpperCase()) {
            case "SUMMER10": return 10.0; case "RAYA15": return 15.0;
            case "CNY20": return 20.0; case "MERDEKA25": return 25.0;
            case "WELCOME5": return 5.0; default: return 0.0;
        }
    }

    @Override
    public String toString() {
        StringBuilder sb = new StringBuilder();
        if (gpsRental) sb.append("GPS "); if (insurancePackage) sb.append("Insurance ");
        if (childSeat) sb.append("ChildSeat ");
        if (!discountCode.isEmpty()) sb.append("| Discount:").append(discountCode).append("(").append(discountPercent).append("%)");
        return sb.length() > 0 ? sb.toString().trim() : "None";
    }
}
