package carrentalsystem;

public class EconomyCar extends Vehicle {
    private boolean hasAirCon;

    public EconomyCar() { super(); this.hasAirCon = true; }

    public EconomyCar(String id, String brand, String model, int year, String plate, String trans,
                      int seats, double mileage, String status, double rate, boolean hasAirCon) {
        super(id, brand, model, year, plate, trans, seats, mileage, status, rate);
        this.hasAirCon = hasAirCon;
    }

    public boolean isHasAirCon() { return hasAirCon; }
    public void setHasAirCon(boolean hasAirCon) { this.hasAirCon = hasAirCon; }

    @Override public String getCategory() { return "Economy"; }

    @Override
    public double calculateRentalCost(int days) { return getDailyRate() * days; }

    @Override public String toString() { return super.toString() + " | AirCon:" + (hasAirCon ? "Yes" : "No"); }

    @Override public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof EconomyCar)) return false;
        return super.equals(obj);
    }
}
