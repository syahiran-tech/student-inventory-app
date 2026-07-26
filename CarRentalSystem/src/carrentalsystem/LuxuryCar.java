package carrentalsystem;

public class LuxuryCar extends Vehicle {
    private boolean hasChauffeur;

    public LuxuryCar() { super(); this.hasChauffeur = false; }

    public LuxuryCar(String id, String brand, String model, int year, String plate, String trans,
                     int seats, double mileage, String status, double rate, boolean hasChauffeur) {
        super(id, brand, model, year, plate, trans, seats, mileage, status, rate);
        this.hasChauffeur = hasChauffeur;
    }

    public boolean isHasChauffeur() { return hasChauffeur; }
    public void setHasChauffeur(boolean hasChauffeur) { this.hasChauffeur = hasChauffeur; }

    @Override public String getCategory() { return "Luxury"; }

    @Override
    public double calculateRentalCost(int days) {
        double cost = getDailyRate() * days * 1.20;
        if (hasChauffeur) cost += 150.0 * days;
        return cost;
    }

    @Override public String toString() { return super.toString() + " | Chauffeur:" + (hasChauffeur ? "Yes" : "No"); }

    @Override public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof LuxuryCar)) return false;
        return super.equals(obj);
    }
}
